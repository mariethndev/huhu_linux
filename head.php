<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Huhu - Plateforme d'enchères de chevaux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/huhu/huhu/assets/style.css">
  <link rel="icon" type="image/png" href="/huhu/huhu/img/favicon.png">
</head>

<body>

<header class="main-header">

  <div class="burger" id="burger">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div class="logo">
    <a href="/huhu/huhu/views/homepage.php">
      <img src="/huhu/huhu/img/huhulogo.png" alt="Logo Huhu">
    </a>
  </div>

  <nav class="desktop-nav">
    <a href="/huhu/huhu/views/homepage.php">Accueil</a>
    <a href="/huhu/huhu/views/buy_a_horse.php">Enchères</a>

  <?php if (trim(strtolower($_SESSION['role'] ?? '')) === 'organisateur'): ?>
      <a href="/huhu/huhu/views/organisateur_dashboard.php">Gérer</a>
  <?php endif; ?>

  </nav>

  <div class="header-actions">
    <?php if (!empty($_SESSION['user_id'])): ?>

      <div class="account-dropdown">

        <span class="account-link">
          Mon compte
        </span>

        <div class="account-menu">
          <a href="/huhu/huhu/views/profile.php">Profil</a>
          <a href="/huhu/huhu/views/my_auctions.php">Mes enchères</a>

          <form action="/huhu/huhu/controller/logout.php" method="post">
            <button type="submit" class="dropdown-logout">
              Se déconnecter
            </button>
          </form>
        </div>

      </div>

    <?php else: ?>

      <div class="account-actions">
        <a href="/huhu/huhu/views/register_form.php" class="btn-outline">
          S'inscrire
        </a>
        <a href="/huhu/huhu/views/login_form.php" class="connect-btn">
          Se connecter
        </a>
      </div>

    <?php endif; ?>
  </div>

</header>

<div class="menu-overlay" id="menuOverlay"></div>

<nav id="mobileMenu" class="mobile-nav">

  <div class="mobile-header">
    <span class="close-menu" id="closeMenu">&times;</span>
  </div>

  <a href="/huhu/huhu/views/homepage.php">Accueil</a>
  <a href="/huhu/huhu/views/buy_a_horse.php">Enchères</a>

  <?php if (($_SESSION['role'] ?? '') === 'organisateur'): ?>
    <a href="/huhu/huhu/views/organisateur_dashboard.php">Gérer</a>
  <?php endif; ?>

  <?php if (!empty($_SESSION['user_id'])): ?>

    <a href="/huhu/huhu/views/profile.php">Mon compte</a>
    <a href="/huhu/huhu/views/my_auctions.php">Mes enchères</a>

    <form action="/huhu/huhu/controller/logout.php" method="post">
      <button type="submit" class="mobile-logout">
        Se déconnecter
      </button>
    </form>

  <?php else: ?>

    <a href="/huhu/huhu/views/register_form.php">S'inscrire</a>
    <a href="/huhu/huhu/views/login_form.php">Se connecter</a>

  <?php endif; ?>

</nav>

<main class="main-content">