<?php
session_start();
require_once "../model/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/register_form.php");
    exit;
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header("Location: ../views/register_form.php?status=danger");
    exit;
}

$name     = trim($_POST['nom'] ?? '');
$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';
$profil   = $_POST['profil'] ?? '';

if (!$name || !$email || !$password || !$profil) {
    header("Location: ../views/register_form.php");
    exit;
}

if ($profil !== "acheteur" && $profil !== "vendeur") {
    header("Location: ../views/register_form.php");
    exit;
}

try {
 
    $stmt = $pdo->prepare("
        SELECT id_user
        FROM users
        WHERE user_email = ?
    ");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        header("Location: ../views/register_form.php");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users
        (user_name, user_email, user_password, user_role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $name,
        $email,
        $hash,
        $profil
    ]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['role']    = $profil;

    unset($_SESSION['csrf_token']);

    header("Location: ../views/homepage.php");
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();  
    exit;
}