<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Insert into database (you can create a contacts table)
    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    
    // For now, just show success message
    $success = 'Thank you for contacting us! We will get back to you within 24 hours.';
}
?>

<div class="container">
    <div class="contact-page">
        <h2 class="section-title">Contact Us</h2>
        
        <?php if($success): ?>
            <div style="background: #4CAF50; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: var(--shadow); max-width: 600px; margin: 0 auto;">
            <form method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">Send Message</button>
            </form>
            
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                <h3 style="color: var(--primary-color); margin-bottom: 15px;">Contact Information</h3>
                <p><strong>Email:</strong> info@moviebook.com</p>
                <p><strong>Phone:</strong> +91 98765 43210</p>
                <p><strong>Address:</strong> 123 Movie Street, Mumbai, India</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>