<?php require_once 'config/database.php'; 
require_once 'includes/header.php'; ?> 
<!DOCTYPE html> <html lang="en"> 
    <head> 
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Privacy Policy - MovieBook</title> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
        <style> * { margin: 0; padding: 0; box-sizing: border-box; } :root { --primary-color: #522566; --secondary-color: #7A3A8E; --success: #4CAF50; --danger: #f44336; --gold: #ffd700; --dark: #333; --light: #fff; --gray: #666; --light-gray: #f4f6fb; } body { font-family: 'Poppins', sans-serif; background: var(--light-gray); min-height: 100vh; }

    .header .container { max-width: 1200px; margin: auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
    .logo { font-size: 28px; font-weight: 700; color: var(--light); text-decoration: none; }
    .logo span { color: var(--gold); }
    .nav { display: flex; gap: 20px; }
    .nav a { color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 500; }
    
    .container-main { max-width: 900px; margin: 50px auto; padding: 0 20px; }
    .card { background: var(--light); padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    .page-title { color: var(--primary-color); font-size: 36px; margin-bottom: 30px; text-align: center; }
    
    .content { line-height: 1.8; color: var(--dark); }
    .content h2 { color: var(--primary-color); margin: 25px 0 15px; font-size: 22px; }
    .content p { margin-bottom: 15px; }
    .content ul { margin-left: 20px; margin-bottom: 15px; }
    .content li { margin-bottom: 8px; }
    
    .footer-links { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; }
    .footer-links a { color: var(--primary-color); text-decoration: none; margin: 0 10px; }
    .footer-links a:hover { text-decoration: underline; }
    
    .footer { background: var(--primary-color); color: rgba(255,255,255,0.8); text-align: center; padding: 20px; margin-top: 50px; }
</style>


<!-- Main Content -->
<div class="container-main">
    <div class="card">
        <h1 class="page-title">Privacy Policy</h1>
        
        <div class="content">
            <h2>1. Information We Collect</h2>
            <p>We collect personal information such as your name, email address, phone number, and payment details when you register on our website or make a booking.</p>
            
            <h2>2. How We Use Your Information</h2>
            <p>Your information is used to:</p>
            <ul>
                <li>Process your movie ticket bookings</li>
                <li>Send booking confirmations and updates</li>
                <li>Improve our services and user experience</li>
                <li>Communicate with you about your account</li>
            </ul>
            
            <h2>3. Data Protection</h2>
            <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            
            <h2>4. Sharing Your Information</h2>
            <p>We do not sell, trade, or otherwise transfer your personal information to outside parties except:</p>
            <ul>
                <li>When necessary to complete your booking (theaters, payment processors)</li>
                <li>When required by law</li>
                <li>To protect our rights and safety</li>
            </ul>
            
            <h2>5. Cookies</h2>
            <p>We use cookies to enhance your browsing experience. You can choose to disable cookies through your browser settings, but this may affect certain features of our website.</p>
            
            <h2>6. Third-Party Links</h2>
            <p>Our website may contain links to third-party websites. We have no control over these sites and are not responsible for their privacy practices.</p>
            
            <h2>7. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access your personal information</li>
                <li>Correct inaccurate data</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of marketing communications</li>
            </ul>
            
            <h2>8. Children's Privacy</h2>
            <p>Our service is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13.</p>
            
            <h2>9. Changes to This Policy</h2>
            <p>We may update this privacy policy periodically. Any changes will be posted on this page with an updated revision date.</p>
            
            <h2>10. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us at:</p>
            <p><strong>Email:</strong> privacy@moviebook.com</p>
            <p><strong>Phone:</strong> +91 98765 43210</p>
        </div>
        
        <div class="footer-links">
            <a href="about.php">About Us</a> |
            <a href="contact.php">Contact</a> |
            <a href="terms.php">Terms & Conditions</a>
        </div>
    </div>
</div>

<!-- Footer -->
<?php require_once 'includes/footer.php'; ?>
</body> </html> 