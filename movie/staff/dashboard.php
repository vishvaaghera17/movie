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

// Get statistics
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'] ?? 0;
$today_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetch_assoc()['count'] ?? 0;
$confirmed_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['count'] ?? 0;
$today_revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE DATE(booking_date) = CURDATE() AND payment_status = 'paid'")->fetch_assoc()['total'] ?? 0;

// Recent bookings
// Recent bookings - Updated JOIN sequence to match schema: bookings -> shows -> screens -> theaters
$recent_bookings = $conn->query("
    SELECT b.*, m.title, s.show_date, s.show_time, t.name as theater_name 
    FROM bookings b 
    LEFT JOIN shows s ON b.show_id = s.show_id 
    LEFT JOIN movies m ON s.movie_id = m.movie_id 
    LEFT JOIN screens sc ON s.screen_id = sc.screen_id
    LEFT JOIN theaters t ON sc.theater_id = t.theater_id 
    ORDER BY b.booking_date DESC LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard - MovieBook</title>

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

.staff-logo i{
    font-size:28px;
    color:#ffd700;
}

.staff-logo span{
    color:#ffd700;
}

.staff-nav{
    display:flex;
    gap:5px;
}

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

.staff-nav a:hover{
    background:rgba(255,255,255,0.15);
    color:var(--light);
}

.staff-nav a.active{
    background:rgba(255,255,255,0.25);
    color:var(--light);
}

.user-section{
    display:flex;
    align-items:center;
    gap:15px;
}

.user-info{
    display:flex;
    align-items:center;
    gap:10px;
    color:var(--light);
}

.user-avatar{
    width:40px;
    height:40px;
    border-radius:50%;
    background:rgba(255,255,255,0.2);
    display:flex;
    align-items:center;
    justify-content:center;
    border:2px solid rgba(255,255,255,0.3);
}

.user-name{
    font-size:14px;
    font-weight:600;
}

.user-role{
    font-size:11px;
    color:rgba(255,255,255,0.7);
    text-transform:uppercase;
}

.logout-btn{
    background:rgba(255,255,255,0.15);
    padding:10px 18px;
    border-radius:8px;
    color:var(--light);
    text-decoration:none;
    font-size:13px;
    display:flex;
    align-items:center;
    gap:8px;
    transition:all 0.3s;
}

.logout-btn:hover{
    background:var(--danger);
}

/* ===== MAIN CONTENT ===== */
.main-content{
    max-width:1400px;
    margin:40px auto;
    padding:0 30px;
}

.page-title{
    font-size:28px;
    font-weight:700;
    color:var(--primary-color);
    margin-bottom:30px;
    display:flex;
    align-items:center;
    gap:12px;
}

/* ===== STATS GRID ===== */
.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:25px;
    margin-bottom:40px;
}

.stat-card{
    background:var(--light);
    border-radius:12px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    display:flex;
    align-items:center;
    gap:20px;
    transition:all 0.3s ease;
    border-left:4px solid var(--primary-color);
}

.stat-card:hover{
    transform:translateY(-5px);
    box-shadow:0 8px 25px rgba(82,37,102,0.15);
}

.stat-card:nth-child(2){border-left-color:var(--success);}
.stat-card:nth-child(3){border-left-color:var(--warning);}
.stat-card:nth-child(4){border-left-color:var(--info);}

.stat-icon{
    width:60px;
    height:60px;
    border-radius:12px;
    background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    color:var(--light);
}

.stat-info h3{
    font-size:32px;
    font-weight:700;
    color:var(--dark);
}

.stat-info p{
    font-size:14px;
    color:var(--gray);
    margin-top:5px;
}

/* ===== CARD ===== */
.card{
    background:var(--light);
    border-radius:12px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    margin-bottom:25px;
}

.card-title{
    font-size:18px;
    font-weight:600;
    color:var(--dark);
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}

.card-title i{
    color:var(--secondary-color);
}

/* ===== DATA TABLE ===== */
.data-table{
    width:100%;
    border-collapse:collapse;
}

.data-table thead{
    background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));
}

.data-table th{
    padding:14px;
    text-align:left;
    font-weight:600;
    color:var(--light);
    font-size:13px;
}

.data-table td{
    padding:14px;
    border-bottom:1px solid #eee;
    font-size:13px;
}

.data-table tbody tr:hover{
    background:#f9f9f9;
}

.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:500;
}

.badge-confirmed{background:#e8f5e9;color:#2e7d32;}
.badge-pending{background:#fff3e0;color:#ef6c00;}
.badge-cancelled{background:#ffebee;color:#c62828;}

/* ===== FOOTER ===== */
.staff-footer{
    background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));
    color:rgba(255,255,255,0.8);
    text-align:center;
    padding:20px;
    margin-top:50px;
}
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
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="check-bookings.php"><i class="fas fa-ticket-alt"></i> Check Bookings</a>
        </nav>

        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="user-name"><?php echo $_SESSION['username']; ?></div>
                    <div class="user-role">Staff</div>
                </div>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</header>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-content">
    <div class="page-title">
        <i class="fas fa-tachometer-alt"></i> Staff Dashboard
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-info">
                <h3><?php echo $today_bookings; ?></h3>
                <p>Today's Bookings</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3><?php echo $confirmed_bookings; ?></h3>
                <p>Confirmed</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="stat-info">
                <h3>₹<?php echo number_format($today_revenue, 0); ?></h3>
                <p>Today's Revenue</p>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <h3 class="card-title"><i class="fas fa-history"></i> Recent Bookings</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Movie</th>
                    <th>Theater</th>
                    <th>Date & Time</th>
                    <th>Seats</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($recent_bookings && $recent_bookings->num_rows > 0): ?>
                    <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $booking['booking_id']; ?></td>
                        <td><?php echo $booking['title']; ?></td>
                        <td><?php echo $booking['theater_name']; ?></td>
                        <td>
                            <?php echo date('d M', strtotime($booking['show_date'])); ?><br>
                            <small><?php echo date('h:i A', strtotime($booking['show_time'])); ?></small>
                        </td>
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
                        <td colspan="7" style="text-align:center;padding:30px;">No bookings found</td>
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