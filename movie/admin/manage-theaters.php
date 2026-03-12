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

$success = '';
$error = '';
/* =========================
   GENERATE SEATS
========================= */
if(isset($_POST['generate_seats'])){

    $screen_id = $_POST['screen_id'];

    $rows = ['A','B','C','D','E','F','G','H'];
    $seatsPerRow = 10;

    foreach($rows as $row){

        for($i=1;$i<=$seatsPerRow;$i++){

            // Check if already exists
            $check = "SELECT seat_id FROM seats 
                      WHERE screen_id=? 
                      AND seat_row=? 
                      AND seat_number=?";

            $stmt = $conn->prepare($check);
            $stmt->bind_param("isi",$screen_id,$row,$i);
            $stmt->execute();
            $res = $stmt->get_result();

            if($res->num_rows == 0){

                $insert = "INSERT INTO seats
                           (screen_id,seat_row,seat_number,seat_type)
                           VALUES (?,?,?,'regular')";

                $stmt2 = $conn->prepare($insert);
                $stmt2->bind_param("isi",$screen_id,$row,$i);
                $stmt2->execute();
            }
        }
    }

    $success = "Seats Generated Successfully!";
}
// Add new theater
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_theater'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $total_screens = $_POST['total_screens'];
    $contact_number = $_POST['contact_number'];
    
    $query = "INSERT INTO theaters (name, address, city, total_screens, contact_number) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssis", $name, $address, $city, $total_screens, $contact_number);
    
    if($stmt->execute()) {
        $success = 'Theater added successfully!';
    } else {
        $error = 'Failed to add theater';
    }
}

// Delete theater
if(isset($_GET['delete'])) {
    $theater_id = $_GET['delete'];
    $conn->query("DELETE FROM theaters WHERE theater_id = $theater_id");
    header('Location: manage-theaters.php');
    exit();
}

/* =========================
   FETCH THEATERS
========================= */
$theaters_query = "SELECT * FROM theaters ORDER BY theater_id DESC";
$theaters_result = $conn->query($theaters_query);

if(!$theaters_result){
    die("Theater Query Failed: " . $conn->error);
}$screens = $conn->query("SELECT * FROM screens ORDER BY screen_id DESC");
require_once '../includes/admin_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Theaters - MovieBook Admin</title>
    <link rel="stylesheet" href="../css/style.css">
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
            --shadow: 0 2px 10px rgba(82, 37, 102, 0.1);
            --border-radius: 10px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: var(--bg-light);
        }
        
        .main-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 { margin: 0; font-size: 24px; }
        .logo a { color: var(--text-light); text-decoration: none; }
        
        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        
        .main-nav a {
            color: var(--text-light);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        
        .main-nav a:hover { background: rgba(255,255,255,0.2); }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: var(--primary-color);
            margin: 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); }
        
        .btn-danger {
            background: var(--danger);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success { background: var(--success); color: white; }
        .alert-error { background: var(--danger); color: white; }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        
        .card {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }
        
        .card h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        .data-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tr:hover { background: #f9f9f9; }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }
        
        .status-active { background: var(--success); }
        
        .action-links a {
            margin-right: 10px;
            text-decoration: none;
        }
        
        .edit-link { color: var(--secondary-color); }
        .delete-link { color: var(--danger); }
        
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
    
    
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-building"></i> Manage Theaters</h2>
        </div>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
          <!-- Generate Seats -->
<div class="card">
    <h3><i class="fas fa-chair"></i> Generate Seats</h3>

    <form method="post">

        <div class="form-group">
            <label>Select Screen</label>

            <select name="screen_id" required>

                <option value="">-- Select Screen --</option>

                <?php while($s = $screens->fetch_assoc()): ?>

                    <option value="<?php echo $s['screen_id']; ?>">
                        <?php echo $s['screen_name']; ?> 
                        (ID: <?php echo $s['screen_id']; ?>)
                    </option>

                <?php endwhile; ?>

            </select>
        </div>

        <button type="submit" name="generate_seats" class="btn-primary" style="width:100%;">
            <i class="fas fa-cogs"></i> Generate Seats
        </button>

    </form>
</div> 
        <!-- Add Theater Form -->
            <div class="card">
                <h3><i class="fas fa-plus-circle"></i> Add New Theater</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Theater Name</label>
                        <input type="text" name="name" required placeholder="e.g., PVR Cinemas">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" required placeholder="Street address">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" required placeholder="e.g., Mumbai">
                    </div>
                    <div class="form-group">
                        <label>Total Screens</label>
                        <input type="number" name="total_screens" required min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" required placeholder="e.g., 022-12345678">
                    </div>
                    <button type="submit" name="add_theater" class="btn-primary" style="width: 100%;">
                        <i class="fas fa-plus"></i> Add Theater
                    </button>
                </form>
            </div>
            
            <!-- Theaters List -->
            <div class="card">
                <h3><i class="fas fa-list"></i> All Theaters</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Screens</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($theaters_result && $theaters_result->num_rows > 0): ?>
                            <?php while($theater = $theaters_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $theater['theater_id']; ?></td>
                                <td><?php echo $theater['name']; ?></td>
                                <td><?php echo $theater['city']; ?></td>
                                <td><?php echo $theater['total_screens']; ?></td>
                                <td><?php echo $theater['contact_number']; ?></td>
                                <td class="action-links">
                                    <a href="?delete=<?php echo $theater['theater_id']; ?>" class="delete-link" onclick="return confirm('Delete this theater?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No theaters found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer class="main-footer">
        <p>&copy; 2024 MovieBook. All rights reserved.</p>
    </footer>
</body>
</html>