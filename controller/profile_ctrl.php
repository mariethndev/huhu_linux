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

//  je  récupère l'id user
$userId = (int)$_SESSION['user_id'];

//  je  génère un token csrf si pas déjà présent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {

    //  je  récupère les infos de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT user_name, user_email, user_role
        FROM users
        WHERE id_user = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // si l'utilisateur existe pas → on détruit la session
    if (!$user) {
        session_destroy();
        header("Location: ../views/login_form.php");
        exit;
    }

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback → retour login
    header("Location: ../views/login_form.php");
    exit;
}