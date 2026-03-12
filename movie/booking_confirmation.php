<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db   = new Database();
$conn = $db->getConnection();

$booking_id = (int)($_GET['booking_id'] ?? 0);

if (!$booking_id) {
    header("Location: index.php");
    exit();
}

// Fetch booking + show + movie + theater + payment details
$query = "
    SELECT
        b.*,
        m.title, m.image_url, m.genre, m.duration,
        s.show_date, s.show_time, s.price,
        t.name  AS theater_name, t.address, t.city,
        sc.screen_name,
        p.razorpay_payment_id, p.payment_method, p.payment_status, p.payment_date
    FROM bookings b
    JOIN shows    s  ON b.show_id    = s.show_id
    JOIN movies   m  ON s.movie_id   = m.movie_id
    JOIN screens  sc ON s.screen_id  = sc.screen_id
    JOIN theaters t  ON sc.theater_id = t.theater_id
    LEFT JOIN payments p ON p.booking_id = b.booking_id
    WHERE b.booking_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result  = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found.");
}

// Fetch booked seat numbers
$seat_query = "
    SELECT se.seat_number, se.seat_row, se.seat_type
    FROM booked_seats bs
    JOIN seats se ON bs.seat_id = se.seat_id
    WHERE bs.booking_id = ?
";
$seat_stmt = $conn->prepare($seat_query);
if (!$seat_stmt) {
    die("Seat query error: " . $conn->error);
}
$seat_stmt->bind_param("i", $booking_id);
$seat_stmt->execute();
$seats_result = $seat_stmt->get_result();
$seats = [];
while ($row = $seats_result->fetch_assoc()) {
    $seats[] = $row['seat_row'] . $row['seat_number'];
}
$seat_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--primary:#522566;--secondary:#7A3A8E;--gold:#ffd700;--gray:#666;--light:#fff;--bg:#f4f6fb}
body{font-family:'Poppins',sans-serif;background:var(--bg);min-height:100vh}

.header{background:linear-gradient(135deg,var(--primary),var(--secondary));padding:15px 0;position:sticky;top:0;z-index:100}
.header .inner{max-width:1200px;margin:auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center}
.logo{font-size:26px;font-weight:700;color:#fff;text-decoration:none}
.logo span{color:var(--gold)}
.nav a{color:rgba(255,255,255,.9);text-decoration:none;margin-left:20px;font-weight:500}

.main{max-width:650px;margin:40px auto;padding:0 20px}

.success-banner{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;border-radius:15px;padding:30px;text-align:center;margin-bottom:25px;box-shadow:0 8px 25px rgba(82,37,102,.3)}
.success-banner .icon{font-size:60px;margin-bottom:10px}
.success-banner h2{font-size:26px;margin-bottom:6px}
.success-banner p{opacity:.85;font-size:14px}

.card{background:#fff;border-radius:15px;padding:28px;box-shadow:0 5px 20px rgba(0,0,0,.08);margin-bottom:20px}
.card-title{font-size:16px;font-weight:600;color:var(--primary);margin-bottom:18px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #eee;padding-bottom:12px}

.info-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f0f0f0;font-size:14px}
.info-row:last-child{border-bottom:none}
.info-row .label{color:var(--gray)}
.info-row .value{font-weight:600;color:#333;text-align:right}

.movie-row{display:flex;gap:15px;margin-bottom:20px}
.movie-row img{width:75px;height:105px;object-fit:cover;border-radius:8px}
.movie-info h3{color:var(--primary);margin-bottom:6px}
.movie-info p{font-size:13px;color:var(--gray);margin-bottom:4px}

.seats-wrap{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.seat-badge{background:var(--primary);color:#fff;padding:6px 14px;border-radius:5px;font-size:13px;font-weight:500}

.total-row{display:flex;justify-content:space-between;font-size:20px;font-weight:700;color:var(--primary);padding-top:14px;margin-top:10px;border-top:2px solid #eee}

.btn-group{display:flex;gap:12px;margin-top:10px}
.btn{flex:1;padding:14px;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;transition:.3s;display:block}
.btn-primary{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
.btn-outline{background:#fff;color:var(--primary);border:2px solid var(--primary)}
.btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,.15)}

@media(max-width:480px){.btn-group{flex-direction:column}.movie-row img{width:60px;height:85px}}
</style>
</head>
<body>



<div class="main">

  <div class="success-banner">
    <div class="icon">✅</div>
    <h2>Booking Confirmed!</h2>
    <p>Your tickets have been booked successfully. Enjoy the show!</p>
  </div>

  <div class="card">
    <div class="card-title"><i class="fas fa-hashtag"></i> Booking Reference</div>
    <div class="info-row">
      <span class="label">Booking ID</span>
      <span class="value">#<?php echo $booking_id; ?></span>
    </div>
    <div class="info-row">
      <span class="label">Booking Date</span>
      <span class="value"><?php echo date('d M Y, h:i A', strtotime($booking['booking_date'])); ?></span>
    </div>
    <div class="info-row">
      <span class="label">Status</span>
      <span class="value" style="color:green">✔ <?php echo ucfirst($booking['status']); ?></span>
    </div>
  </div>

  <div class="card">
    <div class="card-title"><i class="fas fa-film"></i> Movie Details</div>
    <div class="movie-row">
      <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['title']); ?>">
      <div class="movie-info">
        <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
        <p><i class="fas fa-tag"></i> <?php echo htmlspecialchars($booking['genre']); ?></p>
        <p><i class="fas fa-clock"></i> <?php echo $booking['duration']; ?> mins</p>
        <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($booking['theater_name']); ?></p>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['city']); ?></p>
      </div>
    </div>
    <div class="info-row">
      <span class="label">Screen</span>
      <span class="value"><?php echo htmlspecialchars($booking['screen_name']); ?></span>
    </div>
    <div class="info-row">
      <span class="label">Show Date</span>
      <span class="value"><?php echo date('d M Y', strtotime($booking['show_date'])); ?></span>
    </div>
    <div class="info-row">
      <span class="label">Show Time</span>
      <span class="value"><?php echo date('h:i A', strtotime($booking['show_time'])); ?></span>
    </div>
  </div>

  <div class="card">
    <div class="card-title"><i class="fas fa-chair"></i> Seats & Payment</div>
    <div class="info-row">
      <span class="label">Seats Booked</span>
      <span class="value"><?php echo $booking['total_seats']; ?></span>
    </div>
    <?php if (!empty($seats)): ?>
    <div class="seats-wrap">
      <?php foreach($seats as $seat): ?>
        <span class="seat-badge"><?php echo htmlspecialchars($seat); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="info-row" style="margin-top:14px">
      <span class="label">Price per Seat</span>
      <span class="value">₹<?php echo number_format($booking['price'], 2); ?></span>
    </div>
    <div class="info-row">
      <span class="label">Payment Method</span>
      <span class="value"><?php echo ucfirst($booking['payment_method'] ?? 'Razorpay'); ?></span>
    </div>
    <?php if (!empty($booking['razorpay_payment_id'])): ?>
    <div class="info-row">
      <span class="label">Transaction ID</span>
      <span class="value" style="font-size:12px"><?php echo htmlspecialchars($booking['razorpay_payment_id']); ?></span>
    </div>
    <?php endif; ?>
    <div class="total-row">
      <span>Total Paid</span>
      <span>₹<?php echo number_format($booking['total_amount'], 2); ?></span>
    </div>
  </div>

  <div class="btn-group">
    <a href="my-bookings.php" class="btn btn-primary"><i class="fas fa-list"></i> My Bookings</a>
    <a href="index.php"       class="btn btn-outline"><i class="fas fa-home"></i> Go Home</a>
  </div>

</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>