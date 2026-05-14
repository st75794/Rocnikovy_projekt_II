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
$trip_id = (int)$_POST['trip_id'];

$stmt = $conn->prepare("DELETE FROM trips WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $trip_id, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: ../pages/cesty.php?success=deleted");
exit();
?>
