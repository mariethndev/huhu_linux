<?php
session_start();
require_once "../model/config.php";

//  j'accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/register_form.php");
    exit;
}

// vérification du token CSRF pour sécuriser le formulaire

if (
    empty($_POST['csrf_token']) || // je verifie que la req est bien en post
    empty($_SESSION['csrf_token']) || // et est bien dans la session de l'utilisateur
    // je compare les deux tokens CSRF (celui du formulaire et celui en session)
    // si les deux sont différents → le token est invalide → on refuse la requête
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header("Location: ../views/register_form.php?status=danger");
    exit;
}

//  je  récupère les champs du formulaire
$name     = trim($_POST['nom'] ?? '');
$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';
$profil   = $_POST['profil'] ?? '';

// si un champ est vide → retour formulaire
if (!$name || !$email || !$password || !$profil) {
    header("Location: ../views/register_form.php");
    exit;
}

//  je  vérifie que le rôle est valide
if ($profil !== "acheteur" && $profil !== "vendeur") {
    header("Location: ../views/register_form.php");
    exit;
}

try {

    //  je  vérifie si l'email existe déjà
    $stmt = $pdo->prepare("
        SELECT id_user
        FROM users
        WHERE user_email = ?
    ");
    $stmt->execute([$email]);

    // si déjà utilisé → retour formulaire
    if ($stmt->fetch()) {
        header("Location: ../views/register_form.php");
        exit;
    }

    //  je  hash le mot de passe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    //  je  insère le nouvel utilisateur
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

    //  je  connecte direct l'utilisateur après inscription
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['role']    = $profil;

    //  je  supprime le csrf
    unset($_SESSION['csrf_token']);

    // redirection vers l'accueil
    header("Location: ../views/homepage.php");
    exit;

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();  
    exit;
}