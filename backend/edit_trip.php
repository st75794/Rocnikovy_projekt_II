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

// Ověření, že cesta patří přihlášenému uživateli
$check = $conn->prepare("SELECT id FROM trips WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $trip_id, $user_id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    header("Location: ../pages/cesty.php?error=notfound");
    exit();
}
$check->close();

$stmt = $conn->prepare("UPDATE trips SET name=?, destination=?, date_from=?, date_to=?, status=?, notes=? WHERE id=? AND user_id=?");
$stmt->bind_param("ssssssii", $name, $destination, $date_from, $date_to, $status, $notes, $trip_id, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: ../pages/cesty.php?success=edited");
exit();
?>
