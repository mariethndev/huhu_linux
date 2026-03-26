<?php
session_start();
require_once "../model/config.php";

// je accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/create_organisateur.php");
    exit;
}

// je récupère les champs
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['password_confirm'] ?? '';

// si un champ est vide → retour form
if (
    empty($name) ||
    empty($email) ||
    empty($password) ||
    empty($confirm)
) {
    header("Location: ../views/create_organisateur.php");
    exit;
}

// si les mots de passe correspondent pas → retour
if ($password !== $confirm) {
    header("Location: ../views/create_organisateur.php");
    exit;
}

try {

    // j'insère un nouvel organisateur dans la table users
    $stmt = $pdo->prepare("
        INSERT INTO users
        (user_name, user_email, user_password, user_role)
        VALUES (?, ?, ?, 'organisateur')
    ");

    $stmt->execute([
        $name,
        $email,
        password_hash($password, PASSWORD_DEFAULT) // hash du mot de passe en bdd
    ]);

    // redirection après création de l'utilisateur (organisateur)
    header("Location: ../views/create_organisateur.php");
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();
    header("Location: ../views/create_organisateur.php");
    exit;
}