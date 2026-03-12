<?php
require_once 'session.php';
require_once 'config/database.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to book tickets'); window.location.href = 'login.php';</script>";
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$show_id = $_POST['show_id'] ?? 0;
$selected_seats = $_POST['selected_seats'] ?? '';

if(empty($show_id) || empty($selected_seats)) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

$seats_array = explode(',', $selected_seats);
$seats_count = count($seats_array);

// Get show details
$show_query = "SELECT s.*, m.title, t.name as theater_name, sc.screen_name
               FROM shows s
               JOIN movies m ON s.movie_id = m.movie_id
               JOIN theaters t ON s.theater_id = t.theater_id
               JOIN screens sc ON s.screen_id = sc.screen_id
               WHERE s.show_id = $show_id";
$show_result = $conn->query($show_query);
$show = $show_result->fetch_assoc();

if(!$show) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

$total_amount = $seats_count * $show['price'];

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {
    // Create booking
    $booking_id = 'MB' . time() . rand(100, 999);
    $user_id = $_SESSION['user_id'];
    $seat_numbers = $_POST['selected_seats'];
    $total = $_POST['total_amount'];
    
    $stmt = $conn->prepare("INSERT INTO bookings (booking_id, user_id, show_id, seat_numbers, total_seats, total_amount, status, payment_status, booking_date) VALUES (?, ?, ?, ?, ?, ?, 'confirmed', 'paid', NOW())");
    $stmt->bind_param("siisids", $booking_id, $user_id, $show_id, $seat_numbers, $seats_count, $total);
    
    if($stmt->execute()) {
        // Update available seats
        $conn->query("UPDATE shows SET available_seats = available_seats - $seats_count WHERE show_id = $show_id");
        
        echo "<script>alert('Booking Successful! Your Booking ID: $booking_id'); window.location.href = 'my-bookings.php';</script>";
        exit();
    } else {
        $error = "Booking failed! Please try again.";
    }
} ?>

<!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Confirm Booking - MovieBook</title> <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <style> * { margin: 0; padding: 0; box-sizing: border-box; } :root { --primary-color: #522566; --secondary-color: #7A3A8E; --success: #4CAF50; --danger: #f44336; --gold: #ffd700; --dark: #333; --light: #fff; --gray: #666; --light-gray: #f4f6fb; } body { font-family: 'Poppins', sans-serif; background: var(--light-gray); min-height: 100vh; }

Copy code
    .header { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
    .header .container { max-width: 1200px; margin: auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
    .logo { font-size: 28px; font-weight: 700; color: var(--light); text-decoration: none; }
    .logo span { color: var(--gold); }
    .nav { display: flex; gap: 20px; }
    .nav a { color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 500; }
    
    .container-main { max-width: 600px; margin: 40px auto; padding: 0 20px; }
    
    .card { background: var(--light); border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    .card-title { font-size: 24px; color: var(--primary-color); margin-bottom: 25px; text-align: center; }
    
    .movie-summary { display: flex; gap: 15px; margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid #eee; }
    .movie-summary img { width: 80px; height: 110px; object-fit: cover; border-radius: 8px; }
    .movie-summary-details { flex: 1; }
    .movie-summary-details h3 { color: var(--primary-color); margin-bottom: 8px; }
    .movie-summary-details p { color: var(--gray); font-size: 14px; margin-bottom: 5px; }
    
    .price-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
    .price-row:last-of-type { border-bottom: none; font-weight: 700; font-size: 20px; color: var(--primary-color); }
    
    .seats-display { display: flex; flex-wrap: wrap; gap: 8px; margin: 15px 0; }
    .seat-badge { background: var(--primary-color); color: white; padding: 8px 15px; border-radius: 5px; font-size: 14px; }
    
    .btn-confirm { width: 100%; padding: 16px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; margin-top: 20px; transition: 0.3s; }
    .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(82,37,102,0.3); }
    
    .btn-cancel { display: block; text-align: center; margin-top: 15px; color: var(--gray); text-decoration: none; }
    .btn-cancel:hover { color: var(--danger); }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: #f8d7da; color: #721c24; border-left: 4px solid var(--danger); }
</style>
</head> <body> <header class="header"> <div class="container"> <a href="index.php" class="logo">MovieBook <span>🎬</span></a> <nav class="nav"> <a href="index.php">Home</a> <a href="my-bookings.php">My Bookings</a> </nav> </div> </header>

Copy code
<div class="container-main">
    <?php if($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h2 class="card-title"><i class="fas fa-ticket-alt"></i> Confirm Your Booking</h2>
        
        <form method="POST">
            <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
            <input type="hidden" name="selected_seats" value="<?php echo $selected_seats; ?>">
            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
            
            <div class="movie-summary">
                <img src="<?php echo $show['image_url']; ?>" alt="<?php echo $show['title']; ?>">
                <div class="movie-summary-details">
                    <h3><?php echo $show['title']; ?></h3>
                    <p><i class="fas fa-building"></i> <?php echo $show['theater_name']; ?></p>
                    <p><i class="fas fa-desktop"></i> <?php echo $show['screen_name']; ?></p>
                    <p><i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($show['show_date'])); ?> at <?php echo date('h:i A', strtotime($show['show_time'])); ?></p>
                </div>
            </div>
            
            <div class="price-row">
                <span>Price per seat</span>
                <span>₹<?php echo number_format($show['price'], 2); ?></span>
            </div>
            <div class="price-row">
                <span>Number of seats</span>
                <span><?php echo $seats_count; ?></span>
            </div>
            
            <div class="seats-display">
                <?php foreach($seats_array as $seat): ?>
                    <span class="seat-badge"><?php echo $seat; ?></span>
                <?php endforeach; ?>
            </div>
            
            <div class="price-row">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($total_amount, 2); ?></span>
            </div>
            
            <button type="submit" name="confirm_booking" class="btn-confirm">
                <i class="fas fa-check-circle"></i> Confirm Booking
            </button>
        </form>
        
        <a href="select-seats.php?show_id=<?php echo $show_id; ?>" class="btn-cancel">Go Back to Seat Selection</a>
    </div>
</div>
</body> </html> 