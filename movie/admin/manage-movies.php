<?php
require_once '../config/database.php';

// REMOVE session_start() from here - include session.php instead
require_once '../session.php';

// Check admin access
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$movies_query = "SELECT * FROM movies ORDER BY movie_id DESC";
$movies_result = $conn->query($movies_query);

require_once '../includes/admin_header.php';

?>
<style>
    /* ================================
   ADMIN COMMON STYLES
================================ */

.container {
    max-width: 1200px;
    margin: auto;
    padding: 30px 20px;
}

.dashboard h2 {
    font-size: 26px;
    color: #522566;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ================================
   BUTTON
================================ */

.btn-primary {
    background: linear-gradient(135deg, #522566, #7A3A8E);
    color: white;
    padding: 10px 18px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #7A3A8E, #522566);
    transform: translateY(-2px);
}

/* ================================
   DATA TABLE
================================ */

.data-table {
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(82,37,102,0.15);
    overflow-x: auto;
}

.data-table table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.data-table th {
    background: linear-gradient(135deg, #522566, #7A3A8E);
    color: white;
    padding: 14px 10px;
    text-align: left;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #333;
}

.data-table tr:hover {
    background: #f8f4fb;
}

/* ================================
   ACTION LINKS
================================ */

.data-table a {
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
}

.data-table a:hover {
    text-decoration: underline;
}

/* ================================
   STATUS BADGES
================================ */

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
    display: inline-block;
}

.status-showing {
    background: #4CAF50;
}

.status-upcoming {
    background: #ff9800;
}

/* ================================
   RESPONSIVE
================================ */

@media (max-width: 768px) {
    .dashboard h2 {
        font-size: 22px;
    }

    .btn-primary {
        font-size: 13px;
        padding: 8px 14px;
    }
}
</style>
<div class="container">
    <div class="dashboard">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2><i class="fas fa-film"></i> Manage Movies</h2>
            <a href="add-movie.php" class="btn-primary"><i class="fas fa-plus"></i> Add New Movie</a>
        </div>
        
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Language</th>
                        <th>Duration</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($movies_result && $movies_result->num_rows > 0): ?>
                        <?php while($movie = $movies_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $movie['movie_id']; ?></td>
                            <td><?php echo $movie['title']; ?></td>
                            <td><?php echo $movie['genre']; ?></td>
                            <td><?php echo $movie['language']; ?></td>
                            <td><?php echo $movie['duration']; ?> min</td>
                            <td><?php echo $movie['rating']; ?></td>
                            <td>
                            <span class="status-badge <?php echo $movie['status']=='now_showing'?'status-showing':'status-upcoming'; ?>">                                    <?php echo ucwords(str_replace('_', ' ', $movie['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit-movie.php?id=<?php echo $movie['movie_id']; ?>" style="color: var(--secondary-color); margin-right: 10px;"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete-movie.php?id=<?php echo $movie['movie_id']; ?>" style="color: #f44336;" onclick="return confirm('Delete this movie?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px;">No movies found. <a href="add-movie.php">Add one now!</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>