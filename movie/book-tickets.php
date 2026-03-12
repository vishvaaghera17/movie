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

$show_id = isset($_GET['show_id']) ? $_GET['show_id'] : 0;

// Get show details
$show_query = "SELECT s.*, m.title, m.image_url, sc.screen_name, t.name as theater_name, t.address, t.city 
               FROM shows s 
               JOIN movies m ON s.movie_id = m.movie_id 
               JOIN screens sc ON s.screen_id = sc.screen_id 
               JOIN theaters t ON sc.theater_id = t.theater_id 
               WHERE s.show_id = $show_id";
$show_result = $conn->query($show_query);
$show = $show_result->fetch_assoc();

if(!$show) {
    echo "<script>window.location.href = 'movies.php';</script>";
    exit();
}

// Get booked seats for this show
$booked_seats_query = "SELECT seat_id FROM booked_seats WHERE booking_id IN 
                       (SELECT booking_id FROM bookings WHERE show_id = $show_id AND status = 'confirmed')";
$booked_result = $conn->query($booked_seats_query);
$booked_seats = [];
while($bs = $booked_result->fetch_assoc()) {
    $booked_seats[] = $bs['seat_id'];
}

// Get all seats for this screen
// Get all seats for this screen - Updated ORDER BY for numeric sorting
$screen_id = $show['screen_id'];
$seats_query = "SELECT * FROM seats 
                WHERE screen_id = $screen_id 
                ORDER BY seat_row ASC, CAST(seat_number AS UNSIGNED) ASC";
$seats_result = $conn->query($seats_query);
// Handle booking submission
$booking_error = '';
$booking_success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_tickets'])) {
    $selected_seats = [];

    if(!empty($_POST['selected_seats'])){
        $selected_seats = explode(',', $_POST['selected_seats']);
    }    
    if(empty($selected_seats)) {
        $booking_error = 'Please select at least one seat';
    } else {
        $total_seats = count($selected_seats);
        $total_amount = $total_seats * $show['price'];
        
        // Create booking
        $insert_booking = "INSERT INTO bookings (user_id, show_id, total_seats, total_amount, status, payment_status) 
                          VALUES (?, ?, ?, ?, 'confirmed', 'paid')";
        $stmt = $conn->prepare($insert_booking);
        $stmt->bind_param("iiid", $_SESSION['user_id'], $show_id, $total_seats, $total_amount);
        
        if($stmt->execute()) {
            $booking_id = $conn->insert_id;
            
            // Book seats
            foreach($selected_seats as $seat_id) {
                $insert_seat = "INSERT INTO booked_seats (booking_id, seat_id) VALUES (?, ?)";
                $stmt2 = $conn->prepare($insert_seat);
                $stmt2->bind_param("ii", $booking_id, $seat_id);
                $stmt2->execute();
            }
            
            // Update available seats
            $update_seats = "UPDATE shows SET available_seats = available_seats - $total_seats WHERE show_id = $show_id";
            $conn->query($update_seats);
            
            $booking_success = 'Booking successful! Your booking ID is: ' . $booking_id;
        } else {
            $booking_error = 'Booking failed. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="booking-page">
        <?php if($booking_error): ?>
            <div style="background: #f44336; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                <?php echo $booking_error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($booking_success): ?>
            <div style="background: #4CAF50; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                <h3><?php echo $booking_success; ?></h3>
                <a href="my-bookings.php" class="btn-primary" style="display: inline-block; margin-top: 15px; text-decoration: none;">View My Bookings</a>
            </div>
        <?php else: ?>
        
        <!-- Show Details -->
        <div class="booking-header" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: var(--shadow);">
            <div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: center;">
                <img src="<?php echo $show['image_url']; ?>" alt="<?php echo $show['title']; ?>" style="width: 120px; height: 180px; object-fit: cover; border-radius: 5px;">
                <div>
                    <h2 style="color: var(--primary-color); margin-bottom: 10px;"><?php echo $show['title']; ?></h2>
                    <p><strong>Theater:</strong> <?php echo $show['theater_name']; ?></p>
                    <p><strong>Screen:</strong> <?php echo $show['screen_name']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($show['show_date'])); ?></p>
                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($show['show_time'])); ?></p>
                    <p><strong>Price per ticket:</strong> ₹<?php echo number_format($show['price'], 2); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Seat Selection -->
        <div class="seat-selection" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
            <h3 style="color: var(--primary-color); margin-bottom: 20px; text-align: center;">Select Your Seats</h3>
            
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="display: inline-flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
                    <span style="display: flex; align-items: center; gap: 5px;"><span style="width: 20px; height: 20px; background: #ddd; border-radius: 3px; display: inline-block;"></span> Available</span>
                    <span style="display: flex; align-items: center; gap: 5px;"><span style="width: 20px; height: 20px; background: var(--primary-color); border-radius: 3px; display: inline-block;"></span> Selected</span>
                    <span style="display: flex; align-items: center; gap: 5px;"><span style="width: 20px; height: 20px; background: #999; border-radius: 3px; display: inline-block;"></span> Booked</span>
                </div>
            </div>
            
            <!-- Screen -->
            <div style="background: linear-gradient(to bottom, rgba(82, 37, 102, 0.3), transparent); padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 5px;">
                <p style="color: var(--primary-color); font-weight: 600;">SCREEN</p>
            </div>
            
            <!-- Seats -->
            <form method="POST" id="bookingForm">
                <input type="hidden" name="selected_seats" id="selectedSeatsInput" value="">
                
                <div class="seats-container" style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                    <?php 
                    $current_row = '';
                    while($seat = $seats_result->fetch_assoc()): 
                        if($current_row != $seat['seat_row']):
                            if($current_row != '') echo '</div>';
                            $current_row = $seat['seat_row'];
                    ?>
                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                        <span style="width: 30px; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--primary-color);"><?php echo $seat['seat_row']; ?></span>
                        <?php endif; ?>
                        
                        <?php 
                        $is_booked = in_array($seat['seat_id'], $booked_seats);
                        $seat_class = $is_booked ? 'seat booked' : 'seat available';
                        ?>
                        <button type="button" 
                                class="<?php echo $seat_class; ?>"
                                data-seat-id="<?php echo $seat['seat_id']; ?>"
                                data-seat-number="<?php echo $seat['seat_row'] . $seat['seat_number']; ?>"
                                <?php echo $is_booked ? 'disabled' : ''; ?>
                                style="width: 35px; height: 35px; border: none; border-radius: 5px; cursor: <?php echo $is_booked ? 'not-allowed' : 'pointer'; ?>; background: <?php echo $is_booked ? '#999' : '#ddd'; ?>; color: white; font-weight: 500;">
                            <?php echo $seat['seat_number']; ?>
                        </button>
                    <?php endwhile; ?>
                    </div>
                </div>
                
                <!-- Booking Summary -->
                <div class="booking-summary" style="background: var(--bg-light); padding: 20px; border-radius: 10px; margin-top: 30px;">
                    <h4 style="color: var(--primary-color); margin-bottom: 15px;">Booking Summary</h4>
                    <p>Selected Seats: <span id="selectedCount" style="font-weight: 600;">0</span></p>
                    <p>Price per seat: ₹<?php echo number_format($show['price'], 2); ?></p>
                    <p style="font-size: 20px; font-weight: 700; color: var(--primary-color); margin-top: 10px;">
                        Total: ₹<span id="totalAmount">0.00</span>
                    </p>
                    <button type="button" onclick="goToPayment()" class="btn-primary" style="width:100%">
                    Proceed to Payment
                    </button>      
                </div>
            </form>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<script>

let selectedSeats = [];

// Handle seat click
document.querySelectorAll('.seat').forEach(seat => {

    if(seat.classList.contains('booked')) return;

    seat.addEventListener('click', function(){

        const seatId = this.dataset.seatId;
        const seatNumber = this.dataset.seatNumber;

        // If already selected → remove
        if(this.classList.contains('selected')){

            this.classList.remove('selected');
            this.style.background = '#ddd';

            selectedSeats = selectedSeats.filter(id => id !== seatId);

        } 
        // Select seat
        else {

            this.classList.add('selected');
            this.style.background = 'var(--primary-color)';

            selectedSeats.push(seatId);
        }

        updateSummary();

    });

});


// Update booking summary
function updateSummary(){

    const price = <?php echo $show['price']; ?>;
    const total = selectedSeats.length * price;

    document.getElementById('selectedCount').textContent = selectedSeats.length;
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}


// Submit booking
document.getElementById('bookingForm').addEventListener('submit', function(e){

    if(selectedSeats.length === 0){
        alert("Please select at least one seat");
        e.preventDefault();
        return;
    }

    // Send as comma separated (PHP friendly)
    document.getElementById('selectedSeatsInput').value =
        selectedSeats.join(',');

});
function goToPayment(){

if(selectedSeats.length === 0){
    alert("Please select seats");
    return;
}

const seats = selectedSeats.join(',');
const total = selectedSeats.length * <?php echo $show['price']; ?>;

// Redirect to payment page
window.location.href =
    "payment.php?show_id=<?php echo $show_id; ?>&seats=" + seats + "&amount=" + total;
}
</script>
<?php require_once 'includes/footer.php'; ?>