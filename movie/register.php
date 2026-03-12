<?php
require_once 'config/database.php';
require_once 'session.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res1 = $stmt->get_result();

        if($res1->num_rows > 0) {
            $error = 'Username already taken';
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res2 = $stmt->get_result();

            if($res2->num_rows > 0) {
                $error = 'Email already taken';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, user_type, status) VALUES (?, ?, ?, ?, ?, 'user', 'active')");
                $insert->bind_param("sssss", $username, $email, $hashed_password, $full_name, $phone);

                if($insert->execute()) {
                    $success = "Registration successful! Please login.";
                } else {
                    $error = "Registration failed. Try again.";
                }
            }
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align:center;color:var(--primary-color);margin-bottom:25px;">Create Account</h2>

        <?php if($error): ?>
            <div style="background:#f44336;color:#fff;padding:10px;border-radius:5px;margin-bottom:15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div style="background:#4CAF50;color:#fff;padding:10px;border-radius:5px;margin-bottom:15px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="username" required>
                <small id="username_msg"></small>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" required>
                <small id="email_msg"></small>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;" id="submitBtn" disabled>Register</button>

        </form>

        <p style="text-align:center;margin-top:15px;">
            Already have an account?
            <a href="login.php" style="color:var(--secondary-color);">Login</a>
        </p>
    </div>
</div>

<script>
const usernameInput = document.getElementById('username');
const emailInput = document.getElementById('email');
const submitBtn = document.getElementById('submitBtn');

let usernameValid = false;
let emailValid = false;

function checkAvailability(type, value, msgBox) {
    fetch('check_availability.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `type=${type}&value=${value}`
    })
    .then(res => res.text())
    .then(data => {
        if(data === 'taken') {
            msgBox.innerHTML = type + " already taken";
            msgBox.style.color = "red";
            if(type === 'username') usernameValid = false;
            if(type === 'email') emailValid = false;
        } else {
            msgBox.innerHTML = type + " available";
            msgBox.style.color = "green";
            if(type === 'username') usernameValid = true;
            if(type === 'email') emailValid = true;
        }
        submitBtn.disabled = !(usernameValid && emailValid);
    });
}

usernameInput.addEventListener('keyup', () => {
    if(usernameInput.value.length >= 3){
        checkAvailability('username', usernameInput.value, document.getElementById('username_msg'));
    }
});

emailInput.addEventListener('keyup', () => {
    if(emailInput.value.length >= 5){
        checkAvailability('email', emailInput.value, document.getElementById('email_msg'));
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>