<?php require_once 'config/database.php'; 
require_once 'includes/header.php'; ?> 
<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>Terms & Conditions - MovieBook</title> 
         <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
         <style> * { margin: 0; padding: 0; box-sizing: border-box; } :root { --primary-color: #522566; --secondary-color: #7A3A8E; --success: #4CAF50; --danger: #f44336; --gold: #ffd700; --dark: #333; --light: #fff; --gray: #666; --light-gray: #f4f6fb; } body { font-family: 'Poppins', sans-serif; background: var(--light-gray); min-height: 100vh; }


    .header { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
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
</head> 
<body> <!-- Header --> 



<!-- Main Content -->
<div class="container-main">
    <div class="card">
        <h1 class="page-title">Terms & Conditions</h1>
        
        <div class="content">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using MovieBook, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by these terms, please do not use this service.</p>
            
            <h2>2. User Account</h2>
            <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
            <ul>
                <li>You must provide accurate and complete registration information</li>
                <li>You must be at least 18 years old to create an account</li>
                <li>You are responsible for any actions taken under your account</li>
            </ul>
            
            <h2>3. Booking & Reservations</h2>
            <p>All movie ticket bookings made through MovieBook are subject to the following conditions:</p>
            <ul>
                <li>All bookings are final and non-refundable</li>
                <li>Please verify all details before confirming your booking</li>
                <li>You must arrive at the theater at least 30 minutes before the show</li>
                <li>Valid ID may be required at the time of entry</li>
            </ul>
            
            <h2>4. Payment</h2>
            <p>By making a booking, you authorize us to charge your specified payment method for the total amount. All payments are processed securely through our payment partners.</p>
            
            <h2>5. Pricing</h2>
            <p>Ticket prices are subject to change without notice. The price shown at the time of booking is the final price. Additional booking fees may apply.</p>
            
            <h2>6. Cancellation & Refunds</h2>
            <p>Once a ticket is booked, it cannot be cancelled, refunded, or transferred. We strongly recommend that you check your show timing and date before confirming your booking.</p>
            
            <h2>7. Prohibited Activities</h2>
            <p>You may not:</p>
            <ul>
                <li>Use the service for any illegal purpose</li>
                <li>Violate any laws in your jurisdiction</li>
                <li>Attempt to gain unauthorized access to any part of the service</li>
                <li>Interfere with the proper working of the website</li>
                <li>Resell or redistribute tickets purchased through this platform</li>
            </ul>
            
            <h2>8. Intellectual Property</h2>
            <p>All content on MovieBook including logos, images, text, and software is the property of MovieBook and may not be reproduced without prior written permission.</p>
            
            <h2>9. Limitation of Liability</h2>
            <p>MovieBook shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.</p>
            
            <h2>10. User Conduct</h2>
            <p>You agree to behave respectfully when using our services. Any abusive behavior, harassment, or violation of theater policies may result in account termination without refund.</p>
            
            <h2>11. Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. Continued use of MovieBook after any changes constitutes acceptance of the new terms.</p>
            
            <h2>12. Governing Law</h2>
            <p>These terms shall be governed by and construed in accordance with the laws of India. Any disputes arising shall be subject to the jurisdiction of Indian courts.</p>
            
            <h2>13. Contact Information</h2>
            <p>For any questions about these terms, please contact us at:</p>
            <p><strong>Email:</strong> support@moviebook.com</p>
            <p><strong>Phone:</strong> +91 98765 43210</p>
        </div>
        
        <div class="footer-links">
            <a href="about.php">About Us</a> |
            <a href="contact.php">Contact</a> |
            <a href="privacy.php">Privacy Policy</a>
        </div>
    </div>
</div>

<!-- Footer -->
<?php require_once 'includes/footer.php'; ?>

</body> </html>