<?php
require_once '../config/database.php';

// Check if session is already started
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'] ?? 0;
$total_movies = $conn->query("SELECT COUNT(*) as count FROM movies")->fetch_assoc()['count'] ?? 0;
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'] ?? 0;
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'paid'")->fetch_assoc()['total'] ?? 0;
$total_theaters = $conn->query("SELECT COUNT(*) as count FROM theaters")->fetch_assoc()['count'] ?? 0;

require_once '../includes/admin_header.php';
?>

<!-- Inline CSS -->
<style>
/* Paste your full CSS here */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-color: #522566;
    --secondary-color: #7A3A8E;
    --text-light: #ffffff;
    --text-dark: #333333;
    --bg-light: #f5f5f5;
    --bg-white: #ffffff;
    --shadow: 0 2px 10px rgba(82, 37, 102, 0.1);
}

/* ... rest of your CSS ... */

body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Dashboard Styles */
.dashboard {
    padding: 20px 0;
}

.dashboard-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 35px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: var(--shadow);
    color: white;
}

.dashboard-header h2 {
    margin-bottom: 10px;
    font-size: 28px;
}

.dashboard-header p {
    opacity: 0.9;
    font-size: 16px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    padding: 30px 20px;
    border-radius: 10px;
    box-shadow: var(--shadow);
    text-align: center;
    border-bottom: 4px solid var(--primary-color);
    transition: 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(82, 37, 102, 0.2);
}

.stat-number {
    font-size: 38px;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1.2;
    margin-bottom: 10px;
}

.stat-label {
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 500;
}

.stat-icon {
    font-size: 36px;
    color: var(--secondary-color);
    margin-bottom: 15px;
}

/* Admin Menu */
.admin-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.menu-card {
    background: white;
    padding: 35px 25px;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    box-shadow: var(--shadow);
    transition: 0.3s;
    border: 2px solid transparent;
}

.menu-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 5px 20px rgba(82, 37, 102, 0.2);
    border-color: var(--primary-color);
}

.menu-card i {
    font-size: 40px;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.menu-card h3 {
    color: var(--primary-color);
    margin-bottom: 10px;
    font-size: 18px;
}

.menu-card p {
    color: #666;
    font-size: 14px;
    margin: 0;
}
</style>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <p>Welcome back, <?php echo $_SESSION['username']; ?>!</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-film stat-icon"></i>
                <div class="stat-number"><?php echo $total_movies; ?></div>
                <div class="stat-label">Total Movies</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-ticket-alt stat-icon"></i>
                <div class="stat-number"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-rupee-sign stat-icon"></i>
                <div class="stat-number">₹<?php echo number_format($total_revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-building stat-icon"></i>
                <div class="stat-number"><?php echo $total_theaters; ?></div>
                <div class="stat-label">Total Theaters</div>
            </div>
        </div>
        
        <h3 style="color: var(--primary-color); margin: 30px 0 20px;">Quick Actions</h3>
        <div class="admin-menu">
            <a href="manage-movies.php" class="menu-card">
                <i class="fas fa-film"></i>
                <h3>Manage Movies</h3>
                <p>Add, edit, delete movies</p>
            </a>
            <a href="manage-theaters.php" class="menu-card">
                <i class="fas fa-building"></i>
                <h3>Manage Theaters</h3>
                <p>Add, edit theaters</p>
            </a>
            <a href="manage-shows.php" class="menu-card">
                <i class="fas fa-clock"></i>
                <h3>Manage Shows</h3>
                <p>Add, edit show timings</p>
            </a>
            <a href="manage-bookings.php" class="menu-card">
                <i class="fas fa-ticket-alt"></i>
                <h3>View Bookings</h3>
                <p>All bookings</p>
            </a>
            <a href="manage-users.php" class="menu-card">
                <i class="fas fa-users"></i>
                <h3>Manage Users</h3>
                <p>User accounts</p>
            </a>
            <a href="reports.php" class="menu-card">
                <i class="fas fa-chart-bar"></i>
                <h3>View Reports</h3>
                <p>Revenue & analytics</p>
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>