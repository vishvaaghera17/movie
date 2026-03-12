<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MovieBook</title>

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
            --light:#fff;
            --danger:#f44336;
        }

        body{
            font-family:'Poppins',sans-serif;
            background:#f4f6fb;
        }

        /* ===== ADMIN HEADER (Updated to Staff Style) ===== */
        .admin-header{
            background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));
            padding:0;
            box-shadow:0 3px 15px rgba(82,37,102,0.3);
            position:sticky;
            top:0;
            z-index:1000;
        }

        .admin-header .container{
            max-width:1400px;
            margin:auto;
            padding:0 30px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            height:65px;
        }

        .admin-logo{
            font-size:24px;
            font-weight:700;
            color:var(--light);
            text-decoration:none;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .admin-logo i{
            font-size:28px;
            color:#ffd700;
        }

        .admin-logo span{
            color:#ffd700;
        }

        .admin-nav{
            display:flex;
            gap:5px;
        }

        .admin-nav a{
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

        .admin-nav a:hover, .admin-nav a.active{
            background:rgba(255,255,255,0.15);
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
    </style>
</head>

<body>

<header class="admin-header">
    <div class="container">
        <a href="dashboard.php" class="admin-logo">
            <i class="fas fa-film"></i> MovieBook <span>Admin</span>
        </a>

        <nav class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage-movies.php"><i class="fas fa-video"></i> Movies</a>
            <a href="manage-theaters.php"><i class="fas fa-building"></i> Theaters</a>
            <a href="manage-shows.php"><i class="fas fa-clock"></i> Shows</a>
            <a href="manage-bookings.php"><i class="fas fa-ticket-alt"></i> Bookings</a>
            <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
            <a href="add-user.php"><i class="fas fa-users"></i> Add User</a>

        </nav>

        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <div class="user-name"><?php echo $_SESSION['username'] ?? 'Admin'; ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</header>