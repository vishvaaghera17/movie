<?php
require_once 'session.php';
require_once 'config/database.php';

session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$booking_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify booking belongs to user
$check_query = "SELECT * FROM bookings WHERE booking_id = $booking_id AND user_id = $user_id";
$result = $conn->query($check_query);

if($result && $result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    
    if($booking['status'] == 'confirmed') {
        // Update booking status
        $update = "UPDATE bookings SET status = 'cancelled', payment_status = 'refunded' WHERE booking_id = $booking_id";
        $conn->query($update);
        
        // Release seats
        $seats_query = "SELECT seat_id FROM booked_seats WHERE booking_id = $booking_id";
        $seats_result = $conn->query($seats_query);
        
        if($seats_result) {
            while($seat = $seats_result->fetch_assoc()) {
                $delete_seat = "DELETE FROM booked_seats WHERE seat_id = " . $seat['seat_id'] . " AND booking_id = $booking_id";
                $conn->query($delete_seat);
            }
        }
        
        // Update available seats
        $update_seats = "UPDATE shows SET available_seats = available_seats + " . $booking['total_seats'] . " WHERE show_id = " . $booking['show_id'];
        $conn->query($update_seats);
        
        $_SESSION['message'] = 'Booking cancelled successfully!';
    } else {
        $_SESSION['error'] = 'This booking cannot be cancelled';
    }
}

header('Location: my-bookings.php');
exit();
?>