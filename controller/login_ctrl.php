<?php
session_start();
require_once "../model/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login_form.php");
    exit;
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    header("Location: ../views/login_form.php");
    exit;
}

$email    = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';

if (!$email || !$password) {
    header("Location: ../views/login_form.php");
    exit;
}

// Je vérifie valider que l’email est bien au bon format
// Si l’email n’est pas valide, je renvoie l’utilisateur au formulaire
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/login_form.php");
    exit;
}

try {
    // Récupère un utilisateur depuis users à partir de son email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_email = ?");

    // Exécute la requête avec l'email et converti en minuscules
    $stmt->execute([strtolower($email)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifie si l'utilisateur n'existe pas OU si le mot de passe est faux dans ce cas on refuse la connexion
    if (!$user || !password_verify($password, $user['user_password'])) {
        header("Location: ../views/login_form.php");
        exit;
    }

    // Vérifie si le compte est actif
    if ($user['user_is_active'] != 1) {
        header("Location: ../views/login_form.php");
        exit;
    }

    // Régénère l'ID de session pour éviter les attaques de type session fixation
    // true = supprime l'ancien ID de session
    session_regenerate_id(true);

    // Enregistre les informations de l'utilisateur en session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role']    = $user['user_role'];

    // Supprime le token CSRF après utilisation
    unset($_SESSION['csrf_token']);

    // Redirige l'utilisateur selon son rôle
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