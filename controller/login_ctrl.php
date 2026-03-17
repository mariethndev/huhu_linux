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
    $_SESSION['csrf_token'] != $_POST['csrf_token']
) {
    header("Location: ../views/login_form.php?status=danger");
    exit;
}

$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: ../views/login_form.php?status=danger&message=Champs manquants");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/login_form.php?status=danger&message=Email invalide");
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE user_email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['user_psw'])) {

        header("Location: ../views/login_form.php?status=danger&message=Identifiants incorrects");
        exit;
    }

    if ($user['user_is_active'] != 1) {
        header("Location: ../views/login_form.php?status=danger&message=Compte inactif");
        exit;
    }

    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['name']    = $user['user_name'];
    $_SESSION['email']   = $user['user_email'];
    $_SESSION['role']    = $user['user_role'];
    
    if ($user['user_role'] == 'organisateur') {

        header("Location: ../views/organisateur_dashboard.php");
        exit;

    } else {

        header("Location: ../views/homepage.php");
        exit;
    }

} catch (PDOException $e) {

    header("Location: ../views/login_form.php?status=danger&message=Erreur serveur");
    exit;
}