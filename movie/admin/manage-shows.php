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

// Add new show
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_show'])) {
    $movie_id = $_POST['movie_id'];
    $screen_id = $_POST['screen_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];
    
    $query = "INSERT INTO shows (movie_id, screen_id, show_date, show_time, price, available_seats) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iissdi", $movie_id, $screen_id, $show_date, $show_time, $price, $available_seats);
    
    if($stmt->execute()) {
        $success = 'Show added successfully!';
    } else {
        $error = 'Failed to add show';
    }
}

// Delete show
if(isset($_GET['delete'])) {
    $show_id = $_GET['delete'];
    $conn->query("DELETE FROM shows WHERE show_id = $show_id");
    header('Location: manage-shows.php');
    exit();
}

$movies_result = $conn->query("SELECT * FROM movies WHERE status = 'now_showing' ORDER BY title");
$screens_result = $conn->query("SELECT s.*, t.name as theater_name FROM screens s JOIN theaters t ON s.theater_id = t.theater_id ORDER BY t.name, s.screen_name");

$shows_query = "SELECT sh.*, m.title, sc.screen_name, t.name as theater_name 
               FROM shows sh 
               JOIN movies m ON sh.movie_id = m.movie_id 
               JOIN screens sc ON sh.screen_id = sc.screen_id 
               JOIN theaters t ON sc.theater_id = t.theater_id 
               ORDER BY sh.show_date DESC, sh.show_time DESC";
$shows_result = $conn->query($shows_query);
require_once '../includes/admin_header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shows - MovieBook Admin</title>
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
        
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: var(--success); color: white; }
        .alert-error { background: var(--danger); color: white; }
        
        .content-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        
        .card { background: var(--bg-white); border-radius: var(--border-radius); padding: 25px; box-shadow: var(--shadow); }
        .card h3 { color: var(--primary-color); margin-top: 0; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: var(--secondary-color); }
        
        .data-table { width: 100%; border-collapse: collapse; background: var(--bg-white); border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow); }
        .data-table th { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 15px; text-align: left; }
        .data-table td { padding: 15px; border-bottom: 1px solid #eee; }
        .data-table tr:hover { background: #f9f9f9; }
        
        .action-links a { margin-right: 10px; text-decoration: none; }
        .delete-link { color: var(--danger); }
        
        .main-footer { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-light); padding: 20px 0; text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    
    
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-clock"></i> Manage Shows</h2>
        </div>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
            <div class="card">
                <h3><i class="fas fa-plus-circle"></i> Add New Show</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Select Movie</label>
                        <select name="movie_id" required>
                            <option value="">Choose Movie</option>
                            <?php while($movie = $movies_result->fetch_assoc()): ?>
                                <option value="<?php echo $movie['movie_id']; ?>"><?php echo $movie['title']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Screen</label>
                        <select name="screen_id" required>
                            <option value="">Choose Screen</option>
                            <?php while($screen = $screens_result->fetch_assoc()): ?>
                                <option value="<?php echo $screen['screen_id']; ?>"><?php echo $screen['theater_name']; ?> - <?php echo $screen['screen_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Show Date</label>
                        <input type="date" name="show_date" required>
                    </div>
                    <div class="form-group">
                        <label>Show Time</label>
                        <input type="time" name="show_time" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" name="price" required min="0" step="0.01" placeholder="250.00">
                    </div>
                    <div class="form-group">
                        <label>Available Seats</label>
                        <input type="number" name="available_seats" required min="1" value="100">
                    </div>
                    <button type="submit" name="add_show" class="btn-primary" style="width: 100%;">
                        <i class="fas fa-plus"></i> Add Show
                    </button>
                </form>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-list"></i> All Shows</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Movie</th>
                            <th>Theater</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Price</th>
                            <th>Seats</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($shows_result && $shows_result->num_rows > 0): ?>
                            <?php while($show = $shows_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $show['show_id']; ?></td>
                                <td><?php echo $show['title']; ?></td>
                                <td><?php echo $show['theater_name']; ?></td>
                                <td><?php echo date('d M Y', strtotime($show['show_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($show['show_time'])); ?></td>
                                <td>₹<?php echo number_format($show['price'], 2); ?></td>
                                <td><?php echo $show['available_seats']; ?></td>
                                <td class="action-links">
                                    <a href="?delete=<?php echo $show['show_id']; ?>" class="delete-link" onclick="return confirm('Delete this show?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No shows found</td>
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