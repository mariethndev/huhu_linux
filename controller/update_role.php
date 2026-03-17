<?php
session_start();
require_once "../model/config.php";

if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: ../views/profile.php?status=danger");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/profile.php");
    exit;
}

$targetUserId = (int)($_POST['user_id'] ?? 0);
$newRole      = trim($_POST['role'] ?? '');

if ($targetUserId <= 0) {
    header("Location: ../views/profile.php?status=danger");
    exit;
}

if (
    $newRole !== "visiteur" &&
    $newRole !== "acheteur" &&
    $newRole !== "vendeur" &&
    $newRole !== "organisateur"
) {
    header("Location: ../views/profile.php?status=danger");
    exit;
}


if ($targetUserId === $_SESSION['user_id']) {
    header("Location: ../views/profile.php?status=danger&message=Impossible de modifier votre propre rôle");
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE users
        SET user_role = ?
        WHERE id_user = ?
    ");

    $stmt->execute([
        $newRole,
        $targetUserId
    ]);

    header("Location: ../views/profile.php?status=success");
    exit;

} catch (PDOException $e) {
    header("Location: ../views/profile.php?status=danger");
    exit;
}