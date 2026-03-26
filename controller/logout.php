<?php
// je démarre la session
session_start();

// je vide toutes les variables de session
$_SESSION = [];

// je détruit complètement la session
session_destroy();

// je redirige vers la page de login
header("Location: ../views/login_form.php");
exit;