<?php
// démarre la session
session_start();
require_once "../model/config.php";

// j'accepte uniquement les requêtes en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login_form.php");
    exit;
}

// Je vérifie que le token CSRF est présent dans les données envoyées 
// par le formulaire ($_POST) et dans la session ($_SESSION) de l'user.
// et identique entre le formulaire et la session ; 
// sinon la requête est refusée et l'utilisateur est redirigé.

if (
    // je verifie que la requete vient bien du formulaire 
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header("Location: ../views/login_form.php");
    exit;
}

// je récupère les champs du formulaire
$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';

// si un champ est vide → retour formulaire
if (!$email || !$password) {
    header("Location: ../views/login_form.php");
    exit;
}

// je vérifie que les données de l'email sont valides (au bon format)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/login_form.php");
    exit;
}

try {
    // je récupère l'utilisateur via son email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_email = ?");

    // j'executee avec email en minuscule
    $stmt->execute([strtolower($email)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // je vérifie si user existe pas OU mot de passe faux 
    if (!$user || !password_verify($password, $user['user_password'])) {
        header("Location: ../views/login_form.php");
        exit;
    }

    // si le compte de l'utilisateur est désactivé donc ne corrspoond pas à 1 on refuse l'accès 
    if ($user['user_is_active'] != 1) {
        header("Location: ../views/login_form.php");
        exit;
    }

    // je régénère l'id de session
    // je régénère l'id de session pour empêcher les attaques de fixation de session
    // (un attaquant ne peut pas réutiliser un ancien ID pour usurper la session de l'utilisateur)
    // true supprime l'ancien id
    session_regenerate_id(true);

    // je stocke les infos utiles en session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role']    = $user['user_role'];

    // je supprime le token csrf (usage unique)
    unset($_SESSION['csrf_token']);

    // Si l'user n'a pas le role "organisateur"  il y a redirection selon le rôle sur différentes pages d'affichage
    if ($user['user_role'] === 'organisateur') {
        header("Location: ../views/organisateur_dashboard.php");
    } else {
        header("Location: ../views/homepage.php");
    }
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();  
    header("Location: ../views/login_form.php");
    exit;
}