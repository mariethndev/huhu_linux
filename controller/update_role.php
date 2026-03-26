<?php
//  je  démarre la session
session_start();

//  je  charge la config bdd
require_once "../model/config.php";

//  je  vérifie que l'utilisateur est organisateur
if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: ../views/profile.php?status=danger");
    exit;
}

//  je  accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/profile.php");
    exit;
}

//  je  récupère les données
$targetUserId = (int)($_POST['user_id'] ?? 0);
$newRole      = trim($_POST['role'] ?? '');

// si id invalide → erreur
if ($targetUserId <= 0) {
    header("Location: ../views/profile.php?status=danger");
    exit;
}

//  je  vérifie que le rôle est valide
if (
    $newRole !== "visiteur" &&
    $newRole !== "acheteur" &&
    $newRole !== "vendeur" &&
    $newRole !== "organisateur"
) {
    header("Location: ../views/profile.php?status=danger");
    exit;
}

//  je  empêche de modifier son propre rôle
if ($targetUserId === $_SESSION['user_id']) {
    header("Location: ../views/profile.php?status=danger&message=Impossible de modifier votre propre rôle");
    exit;
}

try {

    //  je  met à jour le rôle de l'utilisateur
    $stmt = $pdo->prepare("
        UPDATE users
        SET user_role = ?
        WHERE id_user = ?
    ");

    $stmt->execute([
        $newRole,
        $targetUserId
    ]);

    // redirection succès
    header("Location: ../views/profile.php?status=success");
    exit;

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback → erreur
    header("Location: ../views/profile.php?status=danger");
    exit;
}