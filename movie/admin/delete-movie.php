<?php
require_once '../config/database.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$movie_id = $_GET['id'] ?? 0;

if($movie_id > 0) {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Delete movie (foreign keys will handle related records)
    $query = "DELETE FROM movies WHERE movie_id = $movie_id";
    $conn->query($query);
}

header('Location: manage-movies.php');
exit();
?>