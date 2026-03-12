<?php
require_once 'session.php';
require_once 'config/database.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$show_id = $_GET['show_id'];
$seats   = $_GET['seats'];
$amount  = $_GET['amount'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment</title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
body{
    margin:0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg,#522566,#7A3A8E);
}
.payment-container{
    max-width:1200px;
    margin:auto;
    padding:40px 20px;
}
.payment-card{
    width:420px;
    margin:120px auto;
    background:white;
    padding:40px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    text-align:center;
}
.payment-card h2{
    color:#522566;
    margin-bottom:20px;
}
.amount{
    font-size:28px;
    font-weight:bold;
    color:#522566;
    margin-bottom:20px;
}
.pay-btn{
    background:linear-gradient(135deg,#522566,#7A3A8E);
    color:white;
    border:none;
    padding:14px 30px;
    font-size:16px;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    transition:0.3s;
}
.pay-btn:hover{
    transform:scale(1.05);
    box-shadow:0 6px 20px rgba(0,0,0,0.2);
}
.info{
    color:#666;
    margin-bottom:15px;
}
</style>
</head>
<body>

<div class="payment-container">
<div class="payment-card">
    <h2>Confirm Payment</h2>
    <p class="info">Seats Selected: <b><?php echo htmlspecialchars($seats); ?></b></p>
    <p class="amount">₹<?php echo htmlspecialchars($amount); ?></p>
    <button id="payBtn" class="pay-btn">Pay Now</button>
</div>
</div>

<script>
var options = {
    "key": "rzp_test_SP1G5rUDUyfbHm",
    "amount": "<?php echo $amount * 100; ?>",
    "currency": "INR",
    "name": "Movie Booking",
    "description": "Ticket Payment",
    "handler": function (response) {
        window.location.href =
            "payment_success.php?pid="     + response.razorpay_payment_id +
            "&oid="                         + (response.razorpay_order_id   || '') +
            "&sig="                         + (response.razorpay_signature  || '') +
            "&show_id=<?php echo urlencode($show_id); ?>" +
            "&seats=<?php echo urlencode($seats); ?>" +
            "&amount=<?php echo urlencode($amount); ?>";
    },
    "theme": {
        "color": "#522566"
    }
};

var rzp = new Razorpay(options);
document.getElementById('payBtn').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>

</body>
</html>
<?php require_once 'includes/footer.php'; ?>