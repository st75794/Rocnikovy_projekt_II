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

$stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND destination_id = ?");
$stmt->bind_param("ii", $user_id, $destination_id);

if ($stmt->execute()) {
    echo "Odebráno z oblíbených.";
} else {
    echo "Chyba při odebírání.";
}

$stmt->close();
$conn->close();
?>