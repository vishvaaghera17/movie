<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "movie_booking_db";
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        } catch(Exception $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}


?>
