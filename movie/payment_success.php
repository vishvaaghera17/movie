<?php
require_once 'session.php';
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get DB connection
$database = new Database();
$conn     = $database->getConnection();

if (!$conn) {
    die("Database connection failed.");
}

// 1. Collect data from URL
$razorpay_payment_id = $_GET['pid']     ?? '';
$razorpay_order_id   = $_GET['oid']     ?? '';
$razorpay_signature  = $_GET['sig']     ?? '';
$show_id             = (int)($_GET['show_id'] ?? 0);
$seats_str           = $_GET['seats']   ?? '';
$amount              = $_GET['amount']  ?? 0;
$user_id             = $_SESSION['user_id'];

// Basic guard
if (empty($razorpay_payment_id) || empty($show_id)) {
    die("Invalid payment details.");
}

// 2. Begin transaction
$conn->begin_transaction();

try {

    // 3. Create booking
    $seats_array = explode(',', $seats_str);
    $total_seats = count($seats_array);

    $stmt = $conn->prepare("
        INSERT INTO bookings
            (user_id, show_id, booking_date, total_seats, total_amount, status, payment_status)
        VALUES
            (?, ?, NOW(), ?, ?, 'confirmed', 'paid')
    ");
    $stmt->bind_param("iiid", $user_id, $show_id, $total_seats, $amount);
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();

    // 4. Insert each booked seat
    $seat_stmt = $conn->prepare("
        INSERT INTO booked_seats (booking_id, seat_id)
        VALUES (?, ?)
    ");
    foreach ($seats_array as $seat_id) {
        $seat_id = (int) trim($seat_id);
        $seat_stmt->bind_param("ii", $booking_id, $seat_id);
        $seat_stmt->execute();
    }
    $seat_stmt->close();

    // 5. Insert into payments table
    $payment_method = 'razorpay';
    $payment_status = 'paid';

    $pay_stmt = $conn->prepare("
        INSERT INTO payments
            (booking_id, user_id,
             razorpay_order_id, razorpay_payment_id, razorpay_signature,
             amount, payment_method, payment_status, payment_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $pay_stmt->bind_param(
        "iissdsss",
        $booking_id,
        $user_id,
        $razorpay_order_id,
        $razorpay_payment_id,
        $razorpay_signature,
        $amount,
        $payment_method,
        $payment_status
    );
    $pay_stmt->execute();
    $pay_stmt->close();

    // 6. Update available seats in shows
    $conn->query("UPDATE shows SET available_seats = available_seats - $total_seats WHERE show_id = $show_id");

    // 7. Commit
    $conn->commit();

    // 8. Redirect to confirmation
    header("Location: booking_confirmation.php?booking_id=" . $booking_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment DB Error: " . $e->getMessage());
    header("Location: payment_failed.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>