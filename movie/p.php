<?php
$password = "1711";
$hashed = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
echo $hashed;
?>