<?php
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 900px; margin: 50px auto; padding: 0 20px;">
    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <h1 style="color: #522566; font-size: 36px; margin-bottom: 20px; text-align: center;">About MovieBook</h1>
        
        <div style="text-align: center; margin-bottom: 40px;">
            <span style="font-size: 60px;">🎬</span>
        </div>
        
        <div style="line-height: 1.8; color: #333; text-align: center;">
            <p style="font-size: 18px; margin-bottom: 20px;">
                Welcome to <strong>MovieBook</strong> - your one-stop destination for booking movie tickets online!
            </p>
            
            <p style="margin-bottom: 20px;">
                We are dedicated to providing you with the best movie booking experience. 
                Browse latest movies, check showtimes, select your favorite seats, and book tickets 
                all from the comfort of your home.
            </p>
            
            <h2 style="color: #522566; margin: 30px 0 15px; font-size: 24px;">Our Mission</h2>
            <p style="margin-bottom: 20px;">
                To make movie ticket booking simple, fast, and convenient for everyone.
            </p>
            
            <h2 style="color: #522566; margin: 30px 0 15px; font-size: 24px;">Why Choose Us?</h2>
            <ul style="list-style: none; padding: 0; text-align: left; max-width: 500px; margin: 0 auto;">
                <li style="padding: 10px 0; border-bottom: 1px solid #eee;">✅ Easy online booking</li>
                <li style="padding: 10px 0; border-bottom: 1px solid #eee;">✅ Wide selection of movies</li>
                <li style="padding: 10px 0; border-bottom: 1px solid #eee;">✅ Choose your own seats</li>
                <li style="padding: 10px 0; border-bottom: 1px solid #eee;">✅ Secure payment</li>
                <li style="padding: 10px 0;">✅ Instant booking confirmation</li>
            </ul>
            
            <h2 style="color: #522566; margin: 30px 0 15px; font-size: 24px;">Contact Us</h2>
            <p style="margin-bottom: 10px;">Email: support@moviebook.com</p>
            <p style="margin-bottom: 10px;">Phone: +91 98765 43210</p>
            <p>Address: Mumbai, Maharashtra, India</p>
        </div>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p style="color: #666;">
                <a href="contact.php" style="color: #522566; text-decoration: none; margin: 0 10px;">Contact</a> | 
                <a href="terms.php" style="color: #522566; text-decoration: none; margin: 0 10px;">Terms & Conditions</a> | 
                <a href="privacy.php" style="color: #522566; text-decoration: none; margin: 0 10px;">Privacy Policy</a>
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>