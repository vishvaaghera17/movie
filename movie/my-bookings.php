<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's bookings
$bookings_query = "SELECT b.*, m.title, m.image_url, s.show_date, s.show_time, t.name as theater_name, sc.screen_name 
                   FROM bookings b 
                   JOIN shows s ON b.show_id = s.show_id 
                   JOIN movies m ON s.movie_id = m.movie_id 
                   JOIN screens sc ON s.screen_id = sc.screen_id 
                   JOIN theaters t ON sc.theater_id = t.theater_id 
                   WHERE b.user_id = $user_id 
                   ORDER BY b.booking_date DESC";
$bookings_result = $conn->query($bookings_query);
?>

<div class="container">
    <div class="my-bookings-page">
        <h2 class="section-title">My Bookings</h2>
        
        <?php if($bookings_result && $bookings_result->num_rows > 0): ?>
            <div class="bookings-list" style="display: flex; flex-direction: column; gap: 20px;">
                <?php while($booking = $bookings_result->fetch_assoc()): ?>
                <div class="booking-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow);">
                    <div style="display: flex; flex-wrap: wrap;">
                        <div style="width: 150px; min-height: 200px;">
                            <img src="<?php echo $booking['image_url']; ?>" alt="<?php echo $booking['title']; ?>" style="width: 100%; height: 100%; object-fit: cover; min-height: 200px;">
                        </div>
                        <div style="flex: 1; padding: 20px;">
                            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <h3 style="color: var(--primary-color); margin-bottom: 5px;"><?php echo $booking['title']; ?></h3>
                                    <p style="color: #666;"><?php echo $booking['theater_name']; ?> - <?php echo $booking['screen_name']; ?></p>
                                    <p style="color: #666;"><?php echo date('d M Y', strtotime($booking['show_date'])); ?> at <?php echo date('h:i A', strtotime($booking['show_time'])); ?></p>
                                    <p style="margin-top: 10px;"><strong>Seats:</strong> <?php echo $booking['total_seats']; ?></p>
                                </div>
                                <div style="text-align: right;">
                                    <p style="font-size: 24px; font-weight: 700; color: var(--primary-color);">₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                                    <span style="display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 500; 
                                        background: <?php echo $booking['status'] == 'confirmed' ? '#4CAF50' : ($booking['status'] == 'cancelled' ? '#f44336' : '#ff9800'); ?>;
                                        color: white; margin-top: 10px;">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                    <p style="color: #999; font-size: 12px; margin-top: 5px;">Booking ID: #<?php echo $booking['booking_id']; ?></p>
                                    <p style="color: #999; font-size: 12px;"><?php echo date('d M Y h:i A', strtotime($booking['booking_date'])); ?></p>
                                </div>
                            </div>
                            
                            <?php if($booking['status'] == 'confirmed' && strtotime($booking['show_date']) > time()): ?>
                            <div style="margin-top: 15px;">
                                <a href="cancel-booking.php?id=<?php echo $booking['booking_id']; ?>" onclick="return confirm('Are you sure you want to cancel this booking?')" style="background: #f44336; color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none;">Cancel Booking</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px; box-shadow: var(--shadow);">
                <p style="font-size: 18px; color: #666; margin-bottom: 20px;">You haven't made any bookings yet</p>
                <a href="movies.php" class="btn-primary">Browse Movies</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>