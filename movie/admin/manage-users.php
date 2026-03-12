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

// Get statistics FIRST
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'] ?? 0;
$total_admins = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'admin'")->fetch_assoc()['count'] ?? 0;
$total_staff = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'staff'")->fetch_assoc()['count'] ?? 0;
$total_regular = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'user'")->fetch_assoc()['count'] ?? 0;

// Update user status
if(isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $user_type = $_POST['user_type'];
    $status = $_POST['status'];
    $conn->query("UPDATE users SET user_type = '$user_type', status = '$status' WHERE user_id = $user_id");
    header('Location: manage-users.php');
    exit();
}

// Delete user
if(isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id = $user_id AND user_type != 'admin'");
    header('Location: manage-users.php');
    exit();
}

// Get all users
$users_query = "SELECT * FROM users ORDER BY user_id DESC";
$users_result = $conn->query($users_query);
require_once '../includes/admin_header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: var(--bg-light);
            min-height: 100vh;
        }
        
        /* HEADER STYLES */
        .main-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }
        
        .logo a {
            color: var(--text-light);
            text-decoration: none;
        }
        
        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 15px;
            margin: 0;
            padding: 0;
        }
        
        .main-nav a {
            color: var(--text-light);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
            font-size: 14px;
        }
        
        .main-nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .main-nav a.active {
            background: rgba(255,255,255,0.3);
        }
        
        /* CONTAINER */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        /* PAGE HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: var(--primary-color);
            margin: 0;
            font-size: 28px;
        }
        
        /* STATS ROW */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--bg-white);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-card .label {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* CARD */
        .card {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }
        
        .card h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tr:hover {
            background: #f9f9f9;
        }
        
        /* USER AVATAR */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }
        
        /* BADGES */
        .user-type-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .type-admin { background: var(--danger); color: white; }
        .type-staff { background: var(--warning); color: white; }
        .type-user { background: var(--info); color: white; }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-active { background: var(--success); color: white; }
        .status-inactive { background: var(--text-muted); color: white; }
        
        /* ACTION FORM */
        .action-form select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 5px;
            font-family: inherit;
        }
        
        .action-form button {
            padding: 5px 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .delete-btn {
            padding: 5px 12px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-left: 5px;
        }
        
        /* FOOTER */
        .main-footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    
    
    <!-- MAIN CONTENT START -->
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-users"></i> Manage Users</h2>
        </div>
        
        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="number"><?php echo $total_users; ?></div>
                <div class="label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="number" style="color: var(--danger);"><?php echo $total_admins; ?></div>
                <div class="label">Admins</div>
            </div>
            <div class="stat-card">
                <div class="number" style="color: var(--warning);"><?php echo $total_staff; ?></div>
                <div class="label">Staff</div>
            </div>
            <div class="stat-card">
                <div class="number" style="color: var(--info);"><?php echo $total_regular; ?></div>
                <div class="label">Regular Users</div>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="card">
            <h3><i class="fas fa-list"></i> All Users</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($users_result && $users_result->num_rows > 0): ?>
                        <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $user['user_id']; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
                                    <strong><?php echo $user['username']; ?></strong>
                                </div>
                            </td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone'] ?? 'N/A'; ?></td>
                            <td>
                                <span class="user-type-badge type-<?php echo $user['user_type']; ?>">
                                    <?php echo ucfirst($user['user_type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $user['status']; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <form method="POST" class="action-form" style="display: flex;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <select name="user_type">
                                        <option value="user" <?php echo $user['user_type'] == 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="staff" <?php echo $user['user_type'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                        <option value="admin" <?php echo $user['user_type'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <select name="status">
                                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                    <button type="submit" name="update_user"><i class="fas fa-check"></i></button>
                                    <?php if($user['user_type'] != 'admin'): ?>
                                    <a href="?delete=<?php echo $user['user_id']; ?>" class="delete-btn" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px;">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- MAIN CONTENT END -->
    
    <!-- FOOTER START -->
    <footer class="main-footer">
        <p>&copy; 2024 MovieBook. All rights reserved.</p>
    </footer>
    <!-- FOOTER END -->
</body>
</html>