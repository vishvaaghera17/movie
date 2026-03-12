<?php
require_once '../config/database.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'staff') {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$search_result = null;
$booking_id = '';

if(isset($_POST['search_booking']) && !empty($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Corrected JOIN path based on your schema:
    // bookings -> shows -> screens -> theaters
    $sql = "SELECT b.*, m.title, s.show_date, s.show_time, 
                   t.name as theater_name, sc.screen_name,
                   u.username, u.email, u.phone
            FROM bookings b
            LEFT JOIN shows s ON b.show_id = s.show_id
            LEFT JOIN movies m ON s.movie_id = m.movie_id
            LEFT JOIN screens sc ON s.screen_id = sc.screen_id
            LEFT JOIN theaters t ON sc.theater_id = t.theater_id
            LEFT JOIN users u ON b.user_id = u.user_id
            WHERE b.booking_id = ?";
            
    $stmt = $conn->prepare($sql);
    
    if($stmt === false) {
        die("SQL Error: " . $conn->error); 
    }
    
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $search_result = $stmt->get_result();
}

// Updated Today's Bookings query with corrected JOINS
$today_bookings = $conn->query("
    SELECT b.*, m.title, s.show_date, s.show_time, t.name as theater_name
    FROM bookings b
    LEFT JOIN shows s ON b.show_id = s.show_id
    LEFT JOIN movies m ON s.movie_id = m.movie_id
    LEFT JOIN screens sc ON s.screen_id = sc.screen_id
    LEFT JOIN theaters t ON sc.theater_id = t.theater_id
    WHERE DATE(b.booking_date) = CURDATE()
    ORDER BY b.booking_id DESC
");

if(!$today_bookings) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Check Bookings - MovieBook Staff</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

:root{
    --primary-color:#522566;
    --secondary-color:#7A3A8E;
    --success:#4CAF50;
    --danger:#f44336;
    --warning:#ff9800;
    --info:#2196F3;
    --dark:#333;
    --light:#fff;
    --gray:#666;
    --light-gray:#f4f6fb;
}

body{
    font-family:'Poppins',sans-serif;
    background:var(--light-gray);
    min-height:100vh;
}

/* ===== STAFF HEADER ===== */
.staff-header{
    background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));
    padding:0;
    box-shadow:0 3px 15px rgba(82,37,102,0.3);
    position:sticky;
    top:0;
    z-index:1000;
}

.staff-header .container{
    max-width:1400px;
    margin:auto;
    padding:0 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    height:65px;
}

.staff-logo{
    font-size:24px;
    font-weight:700;
    color:var(--light);
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:10px;
}

.staff-logo i{font-size:28px;color:#ffd700;}
.staff-logo span{color:#ffd700;}

.staff-nav{display:flex;gap:5px;}

.staff-nav a{
    color:rgba(255,255,255,0.85);
    text-decoration:none;
    padding:10px 16px;
    border-radius:8px;
    font-size:14px;
    font-weight:500;
    transition:all 0.3s ease;
    display:flex;
    align-items:center;
    gap:8px;
}

.staff-nav a:hover{background:rgba(255,255,255,0.15);color:var(--light);}
.staff-nav a.active{background:rgba(255,255,255,0.25);color:var(--light);}

.user-section{display:flex;align-items:center;gap:15px;}
.user-info{display:flex;align-items:center;gap:10px;color:var(--light);}
.user-avatar{width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;border:2px solid rgba(255,255,255,0.3);}
.user-name{font-size:14px;font-weight:600;}
.user-role{font-size:11px;color:rgba(255,255,255,0.7);text-transform:uppercase;}
.logout-btn{background:rgba(255,255,255,0.15);padding:10px 18px;border-radius:8px;color:var(--light);text-decoration:none;font-size:13px;display:flex;align-items:center;gap:8px;transition:all 0.3s;}
.logout-btn:hover{background:var(--danger);}

/* ===== MAIN CONTENT ===== */
.main-content{max-width:1400px;margin:40px auto;padding:0 30px;}
.page-title{font-size:28px;font-weight:700;color:var(--primary-color);margin-bottom:30px;display:flex;align-items:center;gap:12px;}

/* ===== SEARCH BOX ===== */
.search-card{background:var(--light);border-radius:12px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:30px;}
.search-card h3{color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.search-form{display:flex;gap:15px;flex-wrap:wrap;}
.search-input{flex:1;min-width:200px;padding:14px 18px;border:2px solid #e0e0e0;border-radius:8px;font-size:15px;font-family:inherit;}
.search-input:focus{outline:none;border-color:var(--secondary-color);}
.search-btn{padding:14px 30px;background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));color:var(--light);border:none;border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.3s;}
.search-btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(82,37,102,0.3);}

/* ===== BOOKING DETAIL ===== */
.booking-detail{background:var(--light);border-radius:12px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:30px;border-left:4px solid var(--success);}
.booking-detail h3{color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.booking-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;}
.booking-item{background:#f9f9f9;padding:15px;border-radius:8px;}
.booking-item label{font-size:12px;color:var(--gray);text-transform:uppercase;display:block;margin-bottom:5px;}
.booking-item span{font-size:15px;font-weight:600;color:var(--dark);}
.status-badge{padding:8px 16px;border-radius:20px;font-size:13px;font-weight:500;}
.status-confirmed{background:#e8f5e9;color:#2e7d32;}
.status-pending{background:#fff3e0;color:#ef6c00;}
.status-cancelled{background:#ffebee;color:#c62828;}

/* ===== CARD ===== */
.card{background:var(--light);border-radius:12px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:25px;}
.card-title{font-size:18px;font-weight:600;color:var(--dark);margin-bottom:20px;display:flex;align-items:center;gap:10px;}

/* ===== DATA TABLE ===== */
.data-table{width:100%;border-collapse:collapse;}
.data-table thead{background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));}
.data-table th{padding:14px;text-align:left;font-weight:600;color:var(--light);font-size:13px;}
.data-table td{padding:14px;border-bottom:1px solid #eee;font-size:13px;}
.data-table tbody tr:hover{background:#f9f9f9;}
.badge{padding:5px 10px;border-radius:20px;font-size:11px;font-weight:500;}
.badge-confirmed{background:#e8f5e9;color:#2e7d32;}
.badge-pending{background:#fff3e0;color:#ef6c00;}
.badge-cancelled{background:#ffebee;color:#c62828;}

/* ===== FOOTER ===== */
.staff-footer{background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));color:rgba(255,255,255,0.8);text-align:center;padding:20px;margin-top:50px;}
</style>
</head>

<body>

<!-- ===== STAFF HEADER ===== -->
<header class="staff-header">
    <div class="container">
        <a href="dashboard.php" class="staff-logo">
            <i class="fas fa-film"></i> MovieBook <span>Staff</span>
        </a>

        <nav class="staff-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="check-bookings.php" class="active"><i class="fas fa-ticket-alt"></i> Check Bookings</a>
        </nav>

        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <div>
                    <div class="user-name"><?php echo $_SESSION['username']; ?></div>
                    <div class="user-role">Staff</div>
                </div>
            </div>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</header>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-content">
    <div class="page-title">
        <i class="fas fa-ticket-alt"></i> Check Bookings
    </div>

    <!-- Search Box -->
    <div class="search-card">
        <h3><i class="fas fa-search"></i> Search Booking by ID</h3>
        <form method="POST" class="search-form">
            <input type="text" name="booking_id" class="search-input" placeholder="Enter Booking ID (e.g., 1)" value="<?php echo $booking_id; ?>">
            <button type="submit" name="search_booking" class="search-btn">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    <!-- Search Result -->
    <?php if($search_result && $search_result->num_rows > 0): ?>
        <?php $booking = $search_result->fetch_assoc(); ?>
        <div class="booking-detail">
            <h3><i class="fas fa-check-circle"></i> Booking Found - #<?php echo $booking['booking_id']; ?></h3>
            <div class="booking-grid">
                <div class="booking-item">
                    <label>Movie</label>
                    <span><?php echo $booking['title']; ?></span>
                </div>
                <div class="booking-item">
                    <label>Theater</label>
                    <span><?php echo $booking['theater_name']; ?></span>
                </div>
                <div class="booking-item">
                    <label>Screen</label>
                    <span><?php echo $booking['screen_name']; ?></span>
                </div>
                <div class="booking-item">
                    <label>Show Date</label>
                    <span><?php echo date('d M Y', strtotime($booking['show_date'])); ?></span>
                </div>
                <div class="booking-item">
                    <label>Show Time</label>
                    <span><?php echo date('h:i A', strtotime($booking['show_time'])); ?></span>
                </div>
                <div class="booking-item">
                    <label>Seats</label>
                    <span><?php echo $booking['total_seats']; ?></span>
                </div>
                <div class="booking-item">
                    <label>Amount</label>
                    <span>₹<?php echo number_format($booking['total_amount'], 2); ?></span>
                </div>
                <div class="booking-item">
                    <label>Customer</label>
                    <span><?php echo $booking['username']; ?></span>
                </div>
                <div class="booking-item">
                    <label>Email</label>
                    <span><?php echo $booking['email']; ?></span>
                </div>
                <div class="booking-item">
                    <label>Phone</label>
                    <span><?php echo $booking['phone'] ?? 'N/A'; ?></span>
                </div>
                <div class="booking-item">
                    <label>Status</label>
                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </div>
                <div class="booking-item">
                    <label>Payment</label>
                    <span><?php echo ucfirst($booking['payment_status']); ?></span>
                </div>
            </div>
        </div>
    <?php elseif(isset($_POST['search_booking'])): ?>
        <div class="booking-detail" style="border-left-color:var(--danger);">
            <h3><i class="fas fa-times-circle"></i> No Booking Found</h3>
            <p>No booking found with ID: <?php echo $booking_id; ?></p>
        </div>
    <?php endif; ?>

    <!-- Today's Bookings -->
    <div class="card">
        <h3 class="card-title"><i class="fas fa-calendar-day"></i> Today's Bookings</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Movie</th>
                    <th>Theater</th>
                    <th>Time</th>
                    <th>Seats</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($today_bookings && $today_bookings->num_rows > 0): ?>
                    <?php while($booking = $today_bookings->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $booking['booking_id']; ?></td>
                        <td><?php echo $booking['title']; ?></td>
                        <td><?php echo $booking['theater_name']; ?></td>
                        <td><?php echo date('h:i A', strtotime($booking['show_time'])); ?></td>
                        <td><?php echo $booking['total_seats']; ?></td>
                        <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:30px;">No bookings for today</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- ===== FOOTER ===== -->
<footer class="staff-footer">
    <p>&copy; 2024 MovieBook Staff Panel. All rights reserved.</p>
</footer>

</body>
</html>