<?php
session_start();
require_once "../model/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login_form.php");
    exit;
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header("Location: ../views/login_form.php");
    exit;
}

$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';

if (!$email || !$password) {
    header("Location: ../views/login_form.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/login_form.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_email = ?");
    $stmt->execute([strtolower($email)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['user_password'])) {
        header("Location: ../views/login_form.php");
        exit;
    }

    if ($user['user_is_active'] != 1) {
        header("Location: ../views/login_form.php");
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role']    = $user['user_role'];

    unset($_SESSION['csrf_token']);

    if ($user['user_role'] === 'organisateur') {
        header("Location: ../views/organisateur_dashboard.php");
    } else {
        header("Location: ../views/homepage.php");
    }
    exit;

} catch (PDOException $e) {
    header("Location: ../views/login_form.php");
    exit;
}