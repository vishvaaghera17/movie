<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch featured movies for carousel
$featured_query = "SELECT * FROM movies WHERE status = 'now_showing' LIMIT 5";
$featured_result = $conn->query($featured_query);

// Fetch all movies
$movies_query = "SELECT * FROM movies WHERE status = 'now_showing' ORDER BY release_date DESC";
$movies_result = $conn->query($movies_query);
?>

<div class="container">
    <!-- Carousel -->
    <div class="carousel">
        <div class="carousel-inner" id="carouselInner">
            <?php $slide = 0; while($featured = $featured_result->fetch_assoc()): ?>
            <div class="carousel-item <?php echo $slide == 0 ? 'active' : ''; ?>">
                <img src="<?php echo $featured['image_url']; ?>" alt="<?php echo $featured['title']; ?>">
                <div class="carousel-caption">
                    <h2><?php echo $featured['title']; ?></h2>
                    <p><?php echo substr($featured['description'], 0, 150); ?>...</p>
                    <a href="movie-details.php?id=<?php echo $featured['movie_id']; ?>" class="btn-primary">Book Now</a>
                </div>
            </div>
            <?php $slide++; endwhile; ?>
        </div>
        <div class="carousel-controls">
            <button class="carousel-btn" onclick="changeSlide(-1)">❮</button>
            <button class="carousel-btn" onclick="changeSlide(1)">❯</button>
        </div>
    </div>

    <!-- Movies Section -->
    <section class="movies-section">
        <h2 class="section-title">Now Showing</h2>
        <div class="movies-grid">
            <?php while($movie = $movies_result->fetch_assoc()): ?>
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
                    <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>">
                        <button class="book-btn">Book Tickets</button>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
