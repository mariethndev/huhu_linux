<?php
session_start();
require_once "../model/config.php";
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/create_organisateur.php");
    exit;
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header("Location: ../views/create_organisateur.php?status=danger&message=Requête invalide");
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
    header("Location: ../views/create_organisateur.php?status=danger&message=Formulaire incomplet");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/create_organisateur.php?status=danger&message=Email invalide");
    exit;
}

if ($password !== $confirm) {
    header("Location: ../views/create_organisateur.php?status=danger&message=Mots de passe différents");
    exit;
}

if (strlen($password) < 8) {
    header("Location: ../views/create_organisateur.php?status=danger&message=Mot de passe trop court");
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
        header("Location: ../views/create_organisateur.php?status=danger&message=Email déjà utilisé");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users
        (
            user_created_at,
            user_is_active,
            user_role,
            user_name,
            user_email,
            user_psw
        )
        VALUES
        (
            NOW(),
            1,
            'organisateur',
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $name,
        $email,
        $hash
    ]);

    header("Location: ../views/create_organisateur.php?status=success&message=Organisateur créé");
    exit;

} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        header("Location: ../views/create_organisateur.php?status=danger&message=Email déjà utilisé");
        exit;
    }

    header("Location: ../views/create_organisateur.php?status=danger&message=Erreur serveur");
    exit;
}