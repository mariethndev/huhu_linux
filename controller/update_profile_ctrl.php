<?php
session_start();
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/profile.php");
    exit;
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header("Location: ../views/update_profile.php?status=danger");
    exit;
}

$userId    = (int) $_SESSION['user_id'];
$userName  = trim($_POST['user_name'] ?? '');
$userEmail = trim($_POST['user_email'] ?? '');


if (empty($userName) || empty($userEmail)) {
    header("Location: ../views/update_profile.php?status=danger");
    exit;
}

if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/update_profile.php?status=danger");
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id_user
        FROM users
        WHERE user_email = ?
        AND id_user != ?
    ");

    $stmt->execute([
        $userEmail,
        $userId
    ]);

    if ($stmt->fetch()) {
        header("Location: ../views/update_profile.php?status=danger&message=Email déjà utilisé");
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET user_name = ?, user_email = ?
        WHERE id_user = ?
    ");

    $stmt->execute([
        $userName,
        $userEmail,
        $userId
    ]);

    header("Location: ../views/update_profile.php?status=success");
    exit;

} catch (PDOException $e) {
    header("Location: ../views/update_profile.php?status=danger&message=Erreur serveur");
    exit;
}