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
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $genre = $_POST['genre'];
    $language = $_POST['language'];
    $release_date = $_POST['release_date'];
    $rating = $_POST['rating'];
    $status = $_POST['status'];
    
    // Image Upload
    $image_name = $_FILES['movie_image']['name'];
    $tmp_name = $_FILES['movie_image']['tmp_name'];
    $upload_path = "../uploads/";

    if(!file_exists($upload_path)){
        mkdir($upload_path, 0777, true);
    }

    $unique_name = time() . "_" . $image_name;
    $image_url = "uploads/" . $unique_name;

    if(move_uploaded_file($tmp_name, $upload_path . $unique_name)) {
        // Fixed: Changed "ssisssss" to "ssssssssi" (9 parameters)
        $query = "INSERT INTO movies (title, description, duration, genre, language, release_date, rating, image_url, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssss", $title, $description, $duration, $genre, $language, $release_date, $rating, $image_url, $status);        
        if($stmt->execute()) {
            $success = 'Movie added successfully!';
        } else {
            $error = 'Failed to add movie';
        }
    } else {
        $error = 'Failed to upload image';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie - MovieBook Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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

        body { font-family: 'Poppins', sans-serif; background: var(--light-gray); min-height: 100vh; }

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

        .admin-logo i { font-size: 28px; color: #ffd700; }
        .admin-logo span { color: #ffd700; }

        .admin-nav { display: flex; gap: 5px; }

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

        .admin-nav a:hover { background: rgba(255,255,255,0.15); color: var(--light); }
        .admin-nav a.active { background: rgba(255,255,255,0.25); color: var(--light); }

        .user-section { display: flex; align-items: center; gap: 15px; }
        .user-info { display: flex; align-items: center; gap: 10px; color: var(--light); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.3); }
        .user-name { font-size: 14px; font-weight: 600; }
        .user-role { font-size: 11px; color: rgba(255,255,255,0.7); text-transform: uppercase; }
        .logout-btn { background: rgba(255,255,255,0.15); padding: 10px 18px; border-radius: 8px; color: var(--light); text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: var(--danger); }

        .main-content { max-width: 900px; margin: 40px auto; padding: 0 30px; }
        .page-title { font-size: 28px; font-weight: 700; color: var(--primary-color); margin-bottom: 30px; display: flex; align-items: center; gap: 12px; }

        .card { background: var(--light); border-radius: 12px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .card-title { font-size: 20px; font-weight: 600; color: var(--dark); margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full-width { grid-column: 1 / -1; }
        
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

        .form-control:focus { outline: none; border-color: var(--secondary-color); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        select.form-control { cursor: pointer; background: var(--light); }

        .btn-group { display: flex; gap: 15px; margin-top: 25px; }
        
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
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(82,37,102,0.3);
        }

        .btn-secondary { background: #6c757d; color: var(--light); }
        .btn-secondary:hover { background: #5a6268; }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid var(--success); }
        .alert-success a { color: #155724; font-weight: 600; margin-left: auto; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid var(--danger); }

        .admin-footer { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: rgba(255,255,255,0.8); text-align: center; padding: 20px; margin-top: 50px; }
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
                <a href="manage-movies.php"><i class="fas fa-film"></i> Movies</a>
                <a href="manage-theaters.php"><i class="fas fa-building"></i> Theaters</a>
                <a href="manage-shows.php"><i class="fas fa-clock"></i> Shows</a>
                <a href="manage-bookings.php"><i class="fas fa-ticket-alt"></i> Bookings</a>
                <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
                <a href="add-user.php"><i class="fas fa-user-plus"></i> Add User</a>
            </nav>

            <div class="user-section">
                <div class="user-info">
                    <div class="user-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="user-name"><?php echo $_SESSION['username']; ?></div>
                        <div class="user-role"><?php echo $_SESSION['user_type']; ?></div>
                    </div>
                </div>
                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="page-title">
            <i class="fas fa-film"></i> Add New Movie
        </div>

        <div class="card">
            <h3 class="card-title">
                <i class="fas fa-plus-circle"></i> Create New Movie
            </h3>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    <?php echo $success; ?>
                    <a href="manage-movies.php">View Movies</a>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Movie Title *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Enter movie title">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Description *</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Enter movie description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Duration (minutes) *</label>
                        <input type="number" name="duration" class="form-control" required placeholder="e.g., 150">
                    </div>
                    
                    <div class="form-group">
                        <label>Genre *</label>
                        <select name="genre" class="form-control" required>
                            <option value="">Select Genre</option>
                            <option value="Action">Action</option>
                            <option value="Comedy">Comedy</option>
                            <option value="Drama">Drama</option>
                            <option value="Horror">Horror</option>
                            <option value="Romance">Romance</option>
                            <option value="Sci-Fi">Sci-Fi</option>
                            <option value="Thriller">Thriller</option>
                            <option value="Adventure">Adventure</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Language *</label>
                        <select name="language" class="form-control" required>
                            <option value="">Select Language</option>
                            <option value="English">English</option>
                            <option value="Hindi">Hindi</option>
                            <option value="Tamil">Tamil</option>
                            <option value="Telugu">Telugu</option>
                            <option value="Malayalam">Malayalam</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Release Date *</label>
                        <input type="date" name="release_date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Rating *</label>
                        <input type="number" name="rating" class="form-control" step="0.1" min="0" max="10" value="0.0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="now_showing">Now Showing</option>
                            <option value="coming_soon">Coming Soon</option>
                            <option value="ended">Ended</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Movie Image *</label>
                        <input type="file" name="movie_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Movie
                    </button>
                    <a href="manage-movies.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="admin-footer">
        <p>&copy; 2024 MovieBook Admin Panel. All rights reserved.</p>
    </footer>

</body>
</html>