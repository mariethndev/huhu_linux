<?php
//  je  démarre la session
session_start();

//  je  charge la config bdd
require_once "../model/config.php";

// si pas connecté → retour login
if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

//  je  accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/profile.php");
    exit;
}

//  je  récupère les infos
$userId    = (int) $_SESSION['user_id'];
$userName  = trim($_POST['user_name'] ?? '');
$userEmail = trim($_POST['user_email'] ?? '');

// si champ vide → retour form
if (!$userName || !$userEmail) {
    header("Location: ../views/update_profile.php");
    exit;
}

try {

    //  je  vérifie que l'email est pas déjà utilisé par un autre user
    $stmt = $pdo->prepare("
        SELECT id_user
        FROM users
        WHERE user_email = ?
        AND id_user != ?
    ");
    $stmt->execute([$userEmail, $userId]);

    // si trouvé → email déjà pris
    if ($stmt->fetch()) {
        header("Location: ../views/update_profile.php");
        exit;
    }

    //  je  met à jour le profil
    $stmt = $pdo->prepare("
        UPDATE users
        SET user_name = ?, user_email = ?
        WHERE id_user = ?
    ");
    $stmt->execute([$userName, $userEmail, $userId]);

    // redirection après update
    header("Location: ../views/update_profile.php");
    exit;

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback → retour form
    header("Location: ../views/update_profile.php");
    exit;
}