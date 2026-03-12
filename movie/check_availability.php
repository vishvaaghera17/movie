<?php
require_once 'session.php';
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$type = $_POST['type'];
$value = trim($_POST['value']);

if($type == 'username'){
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
}
else if($type == 'email'){
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
}

$stmt->bind_param("s", $value);
$stmt->execute();
$result = $stmt->get_result();

echo ($result->num_rows > 0) ? "taken" : "available";
?>