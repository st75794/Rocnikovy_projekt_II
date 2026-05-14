<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../pages/uvod.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($username) || empty($email) || empty($password)) {
        header("Location: ../admin/manage_users.php?error=emptyfields");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $email, $hashed_password, $is_admin);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../admin/manage_users.php?success=added");
    exit();
} else {
    header("Location: ../admin/manage_users.php");
    exit();
}