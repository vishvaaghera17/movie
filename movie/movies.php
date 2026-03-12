<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Get filter parameters
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$language = isset($_GET['language']) ? $_GET['language'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'now_showing';

// Build query
$query = "SELECT * FROM movies WHERE 1=1";
if($genre) $query .= " AND genre = '$genre'";
if($language) $query .= " AND language = '$language'";
if($status) $query .= " AND status = '$status'";
$query .= " ORDER BY release_date DESC";

$result = $conn->query($query);

// Get unique genres and languages for filters
$genres = $conn->query("SELECT DISTINCT genre FROM movies ORDER BY genre");
$languages = $conn->query("SELECT DISTINCT language FROM movies ORDER BY language");
?>

<div class="container">
    <div class="movies-page">
        <h2 class="section-title">All Movies</h2>
        
        <!-- Filters -->
        <div class="filters" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: var(--shadow);">
            <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label>Genre</label>
                    <select name="genre">
                        <option value="">All Genres</option>
                        <?php while($g = $genres->fetch_assoc()): ?>
                            <option value="<?php echo $g['genre']; ?>" <?php echo $genre == $g['genre'] ? 'selected' : ''; ?>>
                                <?php echo $g['genre']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label>Language</label>
                    <select name="language">
                        <option value="">All Languages</option>
                        <?php while($l = $languages->fetch_assoc()): ?>
                            <option value="<?php echo $l['language']; ?>" <?php echo $language == $l['language'] ? 'selected' : ''; ?>>
                                <?php echo $l['language']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label>Status</label>
                    <select name="status">
                        <option value="now_showing" <?php echo $status == 'now_showing' ? 'selected' : ''; ?>>Now Showing</option>
                        <option value="coming_soon" <?php echo $status == 'coming_soon' ? 'selected' : ''; ?>>Coming Soon</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary">Apply Filters</button>
            </form>
        </div>
        
        <!-- Movies Grid -->
        <div class="movies-grid">
            <?php if($result->num_rows > 0): ?>
                <?php while($movie = $result->fetch_assoc()): ?>
                <div class="movie-card">
                    <img src="<?php echo $movie['image_url']; ?>" alt="<?php echo $movie['title']; ?>" class="movie-poster">
                    <div class="movie-info">
                        <h3 class="movie-title"><?php echo $movie['title']; ?></h3>
                        <p class="movie-details">
                            <?php echo $movie['genre']; ?> | <?php echo $movie['language']; ?> | <?php echo $movie['duration']; ?> mins
                        </p>
                        <div class="movie-rating">
                            <span class="rating-star">★</span>
                            <span><?php echo $movie['rating']; ?>/10</span>
                        </div>
                        <span class="status-badge" style="display: inline-block; padding: 5px 10px; background: <?php echo $movie['status'] == 'now_showing' ? '#4CAF50' : '#ff9800'; ?>; color: white; border-radius: 3px; font-size: 12px; margin-bottom: 10px;">
                            <?php echo ucwords(str_replace('_', ' ', $movie['status'])); ?>
                        </span>
                        <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>">
                            <button class="book-btn">Book Tickets</button>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1/-1; padding: 40px;">No movies found</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>