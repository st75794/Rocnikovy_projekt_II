<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/reglog.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/cesty.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name']);
$destination = trim($_POST['destination']);
$date_from = !empty($_POST['date_from']) ? $_POST['date_from'] : null;
$date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : null;
$status = $_POST['status'];
$notes = trim($_POST['notes']);

$allowed_statuses = ['plánovaná', 'probíhající', 'dokončená'];
if (empty($name) || empty($destination) || !in_array($status, $allowed_statuses)) {
    header("Location: ../pages/cesty.php?error=invalid");
    exit();
}

$stmt = $conn->prepare("INSERT INTO trips (user_id, name, destination, date_from, date_to, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $user_id, $name, $destination, $date_from, $date_to, $status, $notes);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: ../pages/cesty.php?success=added");
exit();
?>
