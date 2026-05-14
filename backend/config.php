<?php
$host = 'localhost';
$db   = 'journeyo';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}
?>
