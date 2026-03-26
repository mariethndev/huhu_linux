<?php
session_start();
require_once "../model/config.php";

// j'accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/register_form.php");
    exit;
}

// je vérifie que le formulaire vient bien de mon site
// je regarde si le token existe dans le formulaire et dans la session
// puis je compare les deux s'ils sont différents ou absent je bloque
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header("Location: ../views/register_form.php?status=danger");
    exit;
}

// je récupère les données envoyées par le formulaire saisit par l'user 
$name     = trim($_POST['nom'] ?? '');
$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';
$profil   = $_POST['profil'] ?? '';

// je vérifie que tous les champs obligatoires sont remplis
if (!$name || !$email || !$password || !$profil) {
    header("Location: ../views/register_form.php");
    exit;
}

// je vérifie que le rôle envoyé est autorisé
// ici seuls "acheteur" ou "vendeur" sont acceptés
// si une autre valeur est envoyée (modifiée ou invalide), je bloque
if ($profil !== "acheteur" && $profil !== "vendeur") {
    header("Location: ../views/register_form.php");
    exit;
}

try {

    // je vérifie si email existe
    $stmt = $pdo->prepare("
        SELECT id_user
        FROM users
        WHERE user_email = ?
    ");
    $stmt->execute([$email]);

    // je vérifie si un utilisateur avec cet email existe déjà
    // si fetch() retourne un résultat c'est que l'email déjà utilisé
    // donc je bloque l'inscription et je renvoie au formulaire
    if ($stmt->fetch()) {
        header("Location: ../views/register_form.php");
        exit;
    }

    // je hash le mot de passe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // j'insère un utilisateur avec les infos
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

    // je connecte directement l'utilisateur après son inscription
    // je stocke son id et son rôle dans la session
    // je récupère l'id du dernier enregistrement inséré en base
     //c'est l'id du nouvel utilisateur que je viens de créer
    $_SESSION['user_id'] = $pdo->lastInsertId();  
    $_SESSION['role']    = $profil;

    // redirection
    header("Location: ../views/homepage.php");
    exit;

} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}