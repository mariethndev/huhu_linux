<?php
session_start();
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

 if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {

     $stmt = $pdo->prepare("
        SELECT user_name, user_email, user_role
        FROM users
        WHERE id_user = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: ../views/login_form.php");
        exit;
    }

} catch (PDOException $e) {

    header("Location: ../views/login_form.php");
    exit;
}