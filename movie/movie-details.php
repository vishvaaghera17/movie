<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

$movie_id = isset($_GET['id']) ? $_GET['id'] : 0;

$query = "SELECT * FROM movies WHERE movie_id = $movie_id";
$result = $conn->query($query);
$movie = $result->fetch_assoc();

if(!$movie) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// Get shows for this movie
$shows_query = "SELECT s.*, sc.screen_name, t.name as theater_name, t.city 
                FROM shows s 
                JOIN screens sc ON s.screen_id = sc.screen_id 
                JOIN theaters t ON sc.theater_id = t.theater_id 
                WHERE s.movie_id = $movie_id AND s.show_date >= CURDATE() AND s.available_seats > 0
                ORDER BY s.show_date, s.show_time";
$shows_result = $conn->query($shows_query);
?>

<div class="container">
    <div class="movie-details-page">
        <!-- Movie Info Section -->
        <div class="movie-details-hero" style="background: linear-gradient(to right, rgba(82, 37, 102, 0.9), rgba(122, 58, 142, 0.7)), url('<?php echo $movie['image_url']; ?>'); background-size: cover; background-position: center; padding: 60px 0; margin-bottom: 40px;">
            <div style="display: flex; gap: 40px; flex-wrap: wrap; align-items: center; max-width: 1200px; margin: auto; padding: 0 20px;">
                <img src="<?php echo $movie['image_url']; ?>" alt="<?php echo $movie['title']; ?>" style="width: 300px; height: 450px; object-fit: cover; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <div class="movie-hero-info" style="color: white; flex: 1; min-width: 300px;">
                    <h1 style="font-size: 42px; margin-bottom: 15px;"><?php echo $movie['title']; ?></h1>
                    <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
                        <span style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;">
                            <span style="color: #ffd700;">★</span> <?php echo $movie['rating']; ?>/10
                        </span>
                        <span style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;"><?php echo $movie['duration']; ?> mins</span>
                        <span style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;"><?php echo $movie['genre']; ?></span>
                        <span style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;"><?php echo $movie['language']; ?></span>
                    </div>
                    <p style="font-size: 18px; line-height: 1.8; margin-bottom: 20px;"><?php echo $movie['description']; ?></p>
                    <p><strong>Release Date:</strong> <?php echo date('d M Y', strtotime($movie['release_date'])); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Shows Section -->
        <div class="shows-section" style="max-width: 1200px; margin: auto; padding: 0 20px;">
            <h2 class="section-title" style="font-size: 28px; color: #522566; margin-bottom: 25px;">Available Shows</h2>
            
            <?php if($shows_result->num_rows > 0): ?>
                <div class="shows-grid" style="display: grid; gap: 20px;">
                    <?php while($show = $shows_result->fetch_assoc()): ?>
                    <div class="show-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                        <div class="show-info">
                            <h3 style="color: #522566; margin-bottom: 5px;"><?php echo $show['theater_name']; ?></h3>
                            <p style="color: #666;"><?php echo $show['screen_name']; ?> | <?php echo $show['city']; ?></p>
                            <p style="color: #666;"><?php echo date('d M Y', strtotime($show['show_date'])); ?> at <?php echo date('h:i A', strtotime($show['show_time'])); ?></p>
                        </div>
                        <div class="show-price" style="text-align: right;">
                            <p style="font-size: 24px; font-weight: 700; color: #522566;">₹<?php echo number_format($show['price'], 2); ?></p>
                            <p style="color: #666; font-size: 14px;"><?php echo $show['available_seats']; ?> seats available</p>
                            <a href="book-tickets.php?show_id=<?php echo $show['show_id']; ?>" style="display: inline-block; margin-top: 10px; background: linear-gradient(135deg, #522566, #7A3A8E); color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600;">Book Now</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">No shows available currently</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>