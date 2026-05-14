<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Nepřihlášený uživatel.";
    exit;
}

if (!isset($_POST['destination_id'])) {
    echo "Chybí ID destinace.";
    exit;
}

$user_id = $_SESSION['user_id'];
$destination_id = intval($_POST['destination_id']);

$stmt = $conn->prepare("INSERT IGNORE INTO favorites (user_id, destination_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $destination_id);

if ($stmt->execute()) {
    echo "Přidáno do oblíbených.";
} else {
    echo "Chyba při ukládání.";
}

$stmt->close();
$conn->close();
?>
