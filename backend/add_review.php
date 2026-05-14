<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/reglog.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$destination_id = (int)$_POST['destination_id'];
$rating = (int)$_POST['rating'];
$review = trim($_POST['review']);

// Validace
if ($rating < 1 || $rating > 5 || empty($review)) {
    header("Location: ../zeme/destinace.php?id=$destination_id&error=invalid");
    exit();
}

// Uložení recenze
$stmt = $conn->prepare("INSERT INTO reviews (user_id, destination_id, rating, review) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $destination_id, $rating, $review);
$stmt->execute();
$stmt->close();

// Získání názvu destinace pro redirect (např. island.php, slovinsko.php)
$stmt = $conn->prepare("SELECT LOWER(name) AS name FROM destinations WHERE id = ?");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$destination_file = $row ? $row['name'] . ".php" : "destinace.php";

$stmt->close();
$conn->close();

header("Location: ../zeme/" . $destination_file);
exit();
?>