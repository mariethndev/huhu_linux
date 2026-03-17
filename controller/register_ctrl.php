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
$profil   = trim($_POST['profil'] ?? '');

$allowedProfiles = ['acheteur', 'vendeur'];

if (
    empty($name) ||
    empty($email) ||
    empty($password) ||
    empty($profil)
) {
    header("Location: ../views/register_form.php?status=danger&message=Champs manquants");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/register_form.php?status=danger&message=Email invalide");
    exit;
}

if ($profil != "acheteur" && $profil != "vendeur") {

    header("Location: ../views/register_form.php?status=danger&message=Profil invalide");
    exit;
}

if (strlen($password) < 8) {
    header("Location: ../views/register_form.php?status=danger&message=Mot de passe trop court");
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
        header("Location: ../views/register_form.php?status=danger&message=Email déjà utilisé");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users
        (
            user_name,
            user_email,
            user_psw,
            user_role,
            user_is_active,
            user_created_at
        )
        VALUES
        (
            ?, ?, ?, ?, 1, NOW()
        )
    ");

    $stmt->execute([
        $name,
        $email,
        $hash,
        $profil
    ]);

   $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['name']    = $name;
    $_SESSION['email']   = $email;
    $_SESSION['role']    = $profil;

    header("Location: ../views/homepage.php?status=success");
    exit;

} catch (PDOException $e) {
    header("Location: ../views/register_form.php?status=danger&message=Erreur serveur");
    exit;
}