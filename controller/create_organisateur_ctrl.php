<?php
session_start();
require_once "../model/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/create_organisateur.php");
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['password_confirm'] ?? '';

if (
    empty($name) ||
    empty($email) ||
    empty($password) ||
    empty($confirm)
) {
    header("Location: ../views/create_organisateur.php");
    exit;
}

if ($password !== $confirm) {
    header("Location: ../views/create_organisateur.php");
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO users
        (user_name, user_email, user_password, user_role)
        VALUES (?, ?, ?, 'organisateur')
    ");

    $stmt->execute([
        $name,
        $email,
        password_hash($password, PASSWORD_DEFAULT)
    ]);

    header("Location: ../views/create_organisateur.php");
    exit;

} catch (PDOException $e) {
    echo $e->getMessage();
    header("Location: ../views/create_organisateur.php");
    exit;
}