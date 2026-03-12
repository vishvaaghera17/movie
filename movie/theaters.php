<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

$theaters_query = "SELECT * FROM theaters ORDER BY name";
$theaters_result = $conn->query($theaters_query);
?>

<div class="container">
    <div class="theaters-page">
        <h2 class="section-title">Our Theaters</h2>
        
        <div class="theaters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php while($theater = $theaters_result->fetch_assoc()): ?>
            <div class="theater-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow);">
                <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 20px; color: white;">
                    <h3 style="margin-bottom: 10px;"><?php echo $theater['name']; ?></h3>
                    <p><?php echo $theater['city']; ?></p>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #666; margin-bottom: 10px;"><?php echo $theater['address']; ?></p>
                    <p><strong>Screens:</strong> <?php echo $theater['total_screens']; ?></p>
                    <p><strong>Contact:</strong> <?php echo $theater['contact_number']; ?></p>
                    <a href="movies.php?theater=<?php echo $theater['theater_id']; ?>" class="btn-primary" style="display: inline-block; margin-top: 15px; text-decoration: none;">View Shows</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>