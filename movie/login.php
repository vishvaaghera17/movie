<?php
require_once 'session.php';
require_once 'config/database.php';

// Redirect logged-in users
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Prepare statement to avoid SQL injection
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Use password_verify to check hashed password
        if(password_verify($password, $user['password'])) {
            // Password correct — set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Redirect based on user type
            if($user['user_type'] == 'admin') {
                header('Location: admin/dashboard.php');
            } elseif($user['user_type'] == 'staff') {
                header('Location: staff/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'User not found';
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 30px;">Login</h2>
        
        <?php if($error): ?>
            <div style="background: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username or Email</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="register.php" style="color: var(--secondary-color);">Register here</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>