<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ⚠️ ZMĚNA – přidán is_admin do SELECT
    $stmt = $conn->prepare("SELECT id, username, password_hash, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // ⚠️ ZMĚNA – správné přiřazení user_id i is_admin
        $stmt->bind_result($user_id, $username, $hashed_password, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // ⚠️ ZMĚNA – korektní ukládání do session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = (bool)$is_admin;

            header("Location: ../pages/uvod.php");
            exit;
        } else {
            header("Location: ../pages/reglog.php?error=wrongpass");
            exit;
        }
    } else {
        header("Location: ../pages/reglog.php?error=noemail");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>