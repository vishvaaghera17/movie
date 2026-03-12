<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieBook - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="index.php">MovieBook</a></h1>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="movies.php">Movies</a></li>
                        <li><a href="theaters.php">Theaters</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="my-bookings.php">My Bookings</a></li>
                            <?php if($_SESSION['user_type'] == 'admin'): ?>
                                <li><a href="admin/dashboard.php">Admin Panel</a></li>
                            <?php elseif($_SESSION['user_type'] == 'staff'): ?>
                                <li><a href="staff/dashboard.php">Staff Panel</a></li>
                            <?php endif; ?>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main class="main-content">
