<?php
require_once 'config/database.php';
require_once 'session.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to book tickets'); window.location.href = 'login.php';</script>";
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$show_id = isset($_GET['show_id']) ? $_GET['show_id'] : 0;

// Get show details
$show_query = "SELECT s.*, m.title, m.image_url, t.name as theater_name, t.address, t.city, sc.screen_name, sc.total_seats
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

// Get already booked seats for this show
$booked_seats_query = "SELECT GROUP_CONCAT(seat_numbers SEPARATOR ',') as booked_seats FROM bookings WHERE show_id = $show_id AND status != 'cancelled'";
$booked_result = $conn->query($booked_seats_query);
$booked_data = $booked_result->fetch_assoc();
$booked_seats = $booked_data['booked_seats'] ? explode(',', $booked_data['booked_seats']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - MovieBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary-color: #522566; --secondary-color: #7A3A8E; --success: #4CAF50; --danger: #f44336; --gold: #ffd700; --dark: #333; --light: #fff; --gray: #666; --light-gray: #f4f6fb; }
        body { font-family: 'Poppins', sans-serif; background: var(--light-gray); min-height: 100vh; }
        
        /* Header */
        .header { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .header .container { max-width: 1200px; margin: auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 28px; font-weight: 700; color: var(--light); text-decoration: none; }
        .logo span { color: var(--gold); }
        .nav { display: flex; gap: 20px; }
        .nav a { color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 500; }
        
        /* Main Content */
        .container-main { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        
        /* Movie Info */
        .movie-info { background: var(--light); border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .movie-info img { width: 80px; height: 110px; object-fit: cover; border-radius: 8px; }
        .movie-info-details { flex: 1; min-width: 200px; }
        .movie-info-details h3 { color: var(--primary-color); margin-bottom: 5px; font-size: 22px; }
        .movie-info-details p { color: var(--gray); font-size: 14px; }
        .movie-info-details span { background: var(--primary-color); color: white; padding: 5px 12px; border-radius: 20px; font-size: 13px; }
        
        /* Screen */
        .screen-container { text-align: center; margin-bottom: 40px; }
        .screen { background: linear-gradient(180deg, transparent, rgba(82,37,102,0.3)); height: 40px; border-radius: 50% 50% 0 0; transform: perspective(200px) rotateX(-20deg); margin: 0 auto 10px; max-width: 400px; }
        .screen-text { color: var(--gray); font-size: 12px; text-transform: uppercase; letter-spacing: 2px; }
        
        /* Seat Legend */
        .seat-legend { display: flex; justify-content: center; gap: 30px; margin-bottom: 30px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--gray); }
        .legend-seat { width: 25px; height: 25px; border-radius: 5px; border: 2px solid; }
        .legend-seat.available { border-color: #ccc; background: white; }
        .legend-seat.selected { border-color: var(--primary-color); background: var(--primary-color); }
        .legend-seat.booked { border-color: var(--danger); background: var(--danger); cursor: not-allowed; }
        
        /* Seats Grid */
        .seats-container { display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .seat-row { display: flex; gap: 8px; align-items: center; }
        .row-label { width: 30px; color: var(--gray); font-weight: 600; font-size: 14px; }
        
        .seat { 
            width: 32px; height: 32px; border-radius: 5px; border: 2px solid #ccc; background: white; cursor: pointer; 
            display: flex; align-items: center; justify-content: center; font-size: 10px; color: transparent; transition: all 0.2s;
        }
        .seat:hover:not(.booked) { transform: scale(1.1); border-color: var(--secondary-color); }
        .seat.selected { background: var(--primary-color); border-color: var(--primary-color); color: white; }
        .seat.booked { background: var(--danger); border-color: var(--danger); cursor: not-allowed; }
        
        /* Price Section */
        .price-section { background: var(--light); border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-top: 30px; }
        .price-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .price-row:last-child { border-bottom: none; font-weight: 700; font-size: 18px; color: var(--primary-color); }
        .selected-seats-list { display: flex; flex-wrap: wrap; gap: 8px; margin: 15px 0; }
        .seat-tag { background: var(--primary-color); color: white; padding: 5px 12px; border-radius: 5px; font-size: 13px; }
        
        /* Button */
        .btn-book { 
            width: 100%; padding: 16px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
            color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; 
            margin-top: 20px; transition: 0.3s;
        }
        .btn-book:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(82,37,102,0.3); }
        .btn-book:disabled { background: #ccc; cursor: not-allowed; transform: none; }
        
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--primary-color); text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">MovieBook <span>🎬</span></a>
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="my-bookings.php">My Bookings</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-main">
        <a href="movie.php?id=<?php echo $show['movie_id']; ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Movie</a>
        
        <!-- Movie Info -->
        <div class="movie-info">
            <img src="<?php echo $show['image_url']; ?>" alt="<?php echo $show['title']; ?>">
            <div class="movie-info-details">
                <h3><?php echo $show['title']; ?></h3>
                <p><?php echo $show['theater_name']; ?> | <?php echo $show['screen_name']; ?></p>
                <p><?php echo date('d M Y', strtotime($show['show_date'])); ?> at <?php echo date('h:i A', strtotime($show['show_time'])); ?></p>
                <span>₹<?php echo $show['price']; ?>/seat</span>
            </div>
        </div>

        <!-- Seat Legend -->
        <div class="seat-legend">
            <div class="legend-item"><div class="legend-seat available"></div> Available</div>
            <div class="legend-item"><div class="legend-seat selected"></div> Selected</div>
            <div class="legend-item"><div class="legend-seat booked"></div> Booked</div>
        </div>

        <!-- Screen -->
        <div class="screen-container">
            <div class="screen"></div>
            <p class="screen-text">Screen This Way</p>
        </div>

        <!-- Seats -->
        <form id="bookingForm" method="POST" action="confirm-booking.php">
            <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
            <input type="hidden" name="selected_seats" id="selectedSeats">
            
            <div class="seats-container">
                <?php
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                $seats_per_row = 10;
                
                foreach($rows as $row): ?>
                    <div class="seat-row">
                        <span class="row-label"><?php echo $row; ?></span>
                        <?php for($i = 1; $i <= $seats_per_row; $i++): 
                            $seat_number = $row . $i;
                            $is_booked = in_array($seat_number, $booked_seats);
                        ?>
                            <div class="seat <?php echo $is_booked ? 'booked' : ''; ?>" 
                                 data-seat="<?php echo $seat_number; ?>"
                                 <?php echo $is_booked ? 'title="Already Booked"' : ''; ?>>
                                <?php echo $i; ?>
                            </div>
                        <?php endfor; ?>
                        <span class="row-label"><?php echo $row; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Price Section -->
            <div class="price-section">
                <div class="price-row">
                    <span>Price per seat</span>
                    <span>₹<?php echo number_format($show['price'], 2); ?></span>
                </div>
                <div class="price-row">
                    <span>Selected Seats</span>
                    <span id="seatCount">0</span>
                </div>
                <div class="price-row">
                    <span>Total Amount</span>
                    <span id="totalPrice">₹0.00</span>
                </div>
                
                <div class="selected-seats-list" id="selectedSeatsList"></div>
                
                <button type="submit" class="btn-book" id="bookBtn" disabled>Select at least 1 seat</button>
            </div>
        </form>
    </div>

    <script>
        const seats = document.querySelectorAll('.seat:not(.booked)');
        const selectedSeats = [];
        const price = <?php echo $show['price']; ?>;
        
        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                const seatNumber = seat.dataset.seat;
                
                if(seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    const index = selectedSeats.indexOf(seatNumber);
                    if(index > -1) selectedSeats.splice(index, 1);
                } else {
                    seat.classList.add('selected');
                    selectedSeats.push(seatNumber);
                }
                
                updateSummary();
            });
        });
        
        function updateSummary() {
            document.getElementById('seatCount').textContent = selectedSeats.length;
            document.getElementById('totalPrice').textContent = '₹' + (selectedSeats.length * price).toFixed(2);
            document.getElementById('selectedSeats').value = selectedSeats.join(',');
            
            const seatsList = document.getElementById('selectedSeatsList');
            seatsList.innerHTML = selectedSeats.map(s => `<span class="seat-tag">${s}</span>`).join('');
            
            const bookBtn = document.getElementById('bookBtn');
            if(selectedSeats.length > 0) {
                bookBtn.disabled = false;
                bookBtn.textContent = `Book ${selectedSeats.length} Seat${selectedSeats.length > 1 ? 's' : ''} - ₹${(selectedSeats.length * price).toFixed(2)}`;
            } else {
                bookBtn.disabled = true;
                bookBtn.textContent = 'Select at least 1 seat';
            }
        }
    </script>
</body>
</html>