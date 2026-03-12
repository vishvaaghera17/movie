<?php
require_once '../config/database.php';

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// 1. Handle Status Updates (Must be before the query)
if(isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $conn->query("UPDATE bookings SET status = '$status' WHERE booking_id = $booking_id");
    header('Location: manage-bookings.php');
    exit();
}

// 2. Statistics Calculation
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'] ?? 0;
$confirmed_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['count'] ?? 0;
$cancelled_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'cancelled'")->fetch_assoc()['count'] ?? 0;
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'paid'")->fetch_assoc()['total'] ?? 0;

// 3. MAIN QUERY: Corrected Join Path for your Schema
$bookings_query = "SELECT b.*, m.title, s.show_date, s.show_time, t.name as theater_name, u.username, u.email 
                   FROM bookings b 
                   LEFT JOIN shows s ON b.show_id = s.show_id 
                   LEFT JOIN movies m ON s.movie_id = m.movie_id 
                   LEFT JOIN screens sc ON s.screen_id = sc.screen_id 
                   LEFT JOIN theaters t ON sc.theater_id = t.theater_id
                   LEFT JOIN users u ON b.user_id = u.user_id 
                   ORDER BY b.booking_date DESC";

$bookings_result = $conn->query($bookings_query);

// Check for SQL errors immediately
if (!$bookings_result) {
    die("SQL Error: " . $conn->error);
}
require_once '../includes/admin_header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #522566;
            --secondary-color: #7A3A8E;
            --text-light: #ffffff;
            --text-dark: #333333;
            --text-muted: #666666;
            --bg-light: #f5f5f5;
            --bg-white: #ffffff;
            --success: #4CAF50;
            --danger: #f44336;
            --warning: #ff9800;
            --info: #2196F3;
            --shadow: 0 2px 10px rgba(82, 37, 102, 0.1);
            --border-radius: 10px;
        }
        
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; background: var(--bg-light); }
        
        .main-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo h1 { margin: 0; font-size: 24px; }
        .logo a { color: var(--text-light); text-decoration: none; }
        .main-nav ul { display: flex; list-style: none; gap: 20px; margin: 0; padding: 0; }
        .main-nav a { color: var(--text-light); text-decoration: none; padding: 8px 15px; border-radius: 5px; transition: 0.3s; }
        .main-nav a:hover { background: rgba(255,255,255,0.2); }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h2 { color: var(--primary-color); margin: 0; }
        
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--bg-white); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; }
        .stat-card .number { font-size: 32px; font-weight: 700; color: var(--primary-color); }
        .stat-card .label { color: var(--text-muted); font-size: 14px; }
        
        .card { background: var(--bg-white); border-radius: var(--border-radius); padding: 25px; box-shadow: var(--shadow); margin-bottom: 30px; }
        .card h3 { color: var(--primary-color); margin-top: 0; margin-bottom: 20px; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 15px; text-align: left; }
        .data-table td { padding: 15px; border-bottom: 1px solid #eee; }
        .data-table tr:hover { background: #f9f9f9; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-confirmed { background: var(--success); color: white; }
        .status-cancelled { background: var(--danger); color: white; }
        .status-pending { background: var(--warning); color: white; }
        
        .payment-paid { color: var(--success); font-weight: 500; }
        .payment-unpaid { color: var(--danger); font-weight: 500; }
        
        .action-form select { padding: 5px 10px; border: 1px solid #ddd; border-radius: 5px; margin-right: 5px; }
        .action-form button { padding: 5px 12px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; }
        
        .main-footer { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-light); padding: 20px 0; text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    
    
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-ticket-alt"></i> Manage Bookings</h2>
        </div>
        
        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="number"><?php echo $total_bookings; ?></div>
                <div class="label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="number" style="color: var(--success);"><?php echo $confirmed_bookings; ?></div>
                <div class="label">Confirmed</div>
            </div>
            <div class="stat-card">
                <div class="number" style="color: var(--danger);"><?php echo $cancelled_bookings; ?></div>
                <div class="label">Cancelled</div>
            </div>
            <div class="stat-card">
                <div class="number">₹<?php echo number_format($total_revenue, 0); ?></div>
                <div class="label">Total Revenue</div>
            </div>
        </div>
        
        <!-- Bookings Table -->
        <div class="card">
            <h3><i class="fas fa-list"></i> All Bookings</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Movie</th>
                        <th>Theater</th>
                        <th>Date & Time</th>
                        <th>Seats</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    <?php if($bookings_result && $bookings_result->num_rows > 0): ?>
        <?php while($booking = $bookings_result->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $booking['booking_id']; ?></td>
            <td>
                <strong><?php echo htmlspecialchars($booking['username'] ?? 'User Deleted'); ?></strong><br>
                <small><?php echo htmlspecialchars($booking['email'] ?? ''); ?></small>
            </td>
            <td><?php echo htmlspecialchars($booking['title'] ?? 'Movie Deleted'); ?></td>
            <td><?php echo htmlspecialchars($booking['theater_name'] ?? 'N/A'); ?></td>
            <td>
                <?php echo !empty($booking['show_date']) ? date('d M Y', strtotime($booking['show_date'])) : 'N/A'; ?><br>
                <small><?php echo !empty($booking['show_time']) ? date('h:i A', strtotime($booking['show_time'])) : ''; ?></small>
            </td>
            <td><?php echo $booking['total_seats']; ?></td>
            <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
            <td>
                <span class="status-badge status-<?php echo $booking['status']; ?>">
                    <?php echo ucfirst($booking['status']); ?>
                </span>
            </td>
            <td>
                <span style="font-weight:bold; color: <?php echo $booking['payment_status'] == 'paid' ? '#4CAF50' : '#f44336'; ?>;">
                    <?php echo ucfirst($booking['payment_status']); ?>
                </span>
            </td>
            <td>
                <form method="POST" style="display: flex; gap: 5px;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                    <select name="status" style="padding: 4px; border-radius: 4px;">
                        <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" style="background:#522566; color:white; border:none; padding: 5px 10px; border-radius:4px; cursor:pointer;">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="10" style="text-align: center; padding: 40px;">No bookings found.</td></tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
    
    <footer class="main-footer">
        <p>&copy; 2024 MovieBook. All rights reserved.</p>
    </footer>
</body>
</html>