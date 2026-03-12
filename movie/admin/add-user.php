<?php
require_once '../config/database.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    if(empty($username) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required!";
    } else {
        // Check if email already exists
        $check = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
        
        if($check->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, user_type, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("sssss", $username, $email, $phone, $hashed_password, $user_type);
            
            if($stmt->execute()) {
                $success = ucfirst($user_type) . " added successfully!";
            } else {
                $error = "Failed to add user!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - MovieBook Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #522566;
            --secondary-color: #7A3A8E;
            --success: #4CAF50;
            --danger: #f44336;
            --warning: #ff9800;
            --info: #2196F3;
            --dark: #333;
            --light: #fff;
            --gray: #666;
            --light-gray: #f4f6fb;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light-gray);
            min-height: 100vh;
        }

        /* ===== ADMIN HEADER ===== */
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 0;
            box-shadow: 0 3px 15px rgba(82,37,102,0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .admin-header .container {
            max-width: 1400px;
            margin: auto;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 65px;
        }

        .admin-logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--light);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-logo i {
            font-size: 28px;
            color: #ffd700;
        }

        .admin-logo span {
            color: #ffd700;
        }

        .admin-nav {
            display: flex;
            gap: 5px;
        }

        .admin-nav a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-nav a:hover {
            background: rgba(255,255,255,0.15);
            color: var(--light);
        }

        .admin-nav a.active {
            background: rgba(255,255,255,0.25);
            color: var(--light);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--light);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
        }

        .user-role {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
        }

        .logout-btn {
            background: rgba(255,255,255,0.15);
            padding: 10px 18px;
            border-radius: 8px;
            color: var(--light);
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: var(--danger);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            max-width: 700px;
            margin: 40px auto;
            padding: 0 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ===== CARD ===== */
        .card {
            background: var(--light);
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        /* User Type Selection */
        .user-type-group {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .user-type-option {
            flex: 1;
            padding: 20px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .user-type-option:hover {
            border-color: var(--secondary-color);
            background: rgba(82,37,102,0.02);
        }

        .user-type-option input {
            display: none;
        }

        .user-type-option input:checked + .type-content {
            border-color: var(--primary-color);
            background: rgba(82,37,102,0.05);
        }

        .type-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 10px;
            border: 2px solid transparent;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .type-content i {
            font-size: 28px;
            color: var(--primary-color);
        }

        .type-content span {
            font-weight: 600;
            color: var(--dark);
            font-size: 14px;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--light);
            flex: 1;
            justify-content: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(82,37,102,0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: var(--light);
            justify-content: center;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success);
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger);
        }

        /* ===== FOOTER ===== */
        .admin-footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: rgba(255,255,255,0.8);
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
    </style>
</head>

<body>

    <!-- ===== ADMIN HEADER ===== -->
    <header class="admin-header">
        <div class="container">
            <a href="dashboard.php" class="admin-logo">
                <i class="fas fa-film"></i> MovieBook <span>Admin</span>
            </a>

            <nav class="admin-nav">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage-movies.php"><i class="fas fa-film"></i> Movies</a>
                <a href="manage-theaters.php"><i class="fas fa-building"></i> Theaters</a>
                <a href="manage-shows.php"><i class="fas fa-clock"></i> Shows</a>
                <a href="manage-bookings.php"><i class="fas fa-ticket-alt"></i> Bookings</a>
                <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
                <a href="add-user.php" class="active"><i class="fas fa-user-plus"></i> Add User</a>
            </nav>

            <div class="user-section">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="user-name"><?php echo $_SESSION['username']; ?></div>
                        <div class="user-role"><?php echo $_SESSION['user_type']; ?></div>
                    </div>
                </div>
                <a href="../logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
        <div class="page-title">
            <i class="fas fa-user-plus"></i> Add New User
        </div>

        <div class="card">
            <h3 class="card-title">
                <i class="fas fa-user-plus"></i> Create New Account
            </h3>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter username">
                </div>
                
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" required placeholder="Enter email address">
                </div>
                
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="Enter phone number">
                </div>
                
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter password (min 6 characters)" minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Select User Type *</label>
                    <div class="user-type-group">
                        <label class="user-type-option">
                            <input type="radio" name="user_type" value="user" checked>
                            <div class="type-content">
                                <i class="fas fa-user"></i>
                                <span>User</span>
                            </div>
                        </label>
                        
                        <label class="user-type-option">
                            <input type="radio" name="user_type" value="staff">
                            <div class="type-content">
                                <i class="fas fa-user-tie"></i>
                                <span>Staff</span>
                            </div>
                        </label>
                        
                        <label class="user-type-option">
                            <input type="radio" name="user_type" value="admin">
                            <div class="type-content">
                                <i class="fas fa-user-shield"></i>
                                <span>Admin</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create User
                    </button>
                    <a href="manage-users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="admin-footer">
        <p>&copy; 2024 MovieBook Admin Panel. All rights reserved.</p>
    </footer>

</body>
</html>