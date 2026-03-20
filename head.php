<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$role   = strtolower(trim($_SESSION['role'] ?? ''));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Huhu - Plateforme d'enchères de chevaux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/huhu/huhu_linux/assets/style.css">
  <link rel="icon" type="image/png" href="/huhu/huhu_linux/img/favicon.png">
</head>

<body>

<header class="main-header">

  <div class="burger" id="burger">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div class="logo">
    <a href="/huhu/huhu_linux/views/homepage.php">
      <img src="/huhu/huhu_linux/img/huhulogo.png" alt="Logo Huhu">
    </a>
  </div>

  <nav class="desktop-nav">
    <a href="/huhu/huhu_linux/views/homepage.php">Accueil</a>
    <a href="/huhu/huhu_linux/views/buy_a_horse.php">Enchères</a>

    <?php if ($role === 'organisateur'): ?>
      <a href="/huhu/huhu_linux/views/organisateur_dashboard.php">Gérer</a>
    <?php endif; ?>
  </nav>

  <div class="header-actions">

    <?php if ($userId): ?>
      <div class="account-dropdown">
        <span class="account-link">
          <i class="bi bi-person"></i> Mon compte
        </span>

        <div class="account-menu">

          <a href="/huhu/huhu_linux/views/profile.php">
            <i class="bi bi-person-circle"></i> Profil
          </a>

          <a href="/huhu/huhu_linux/views/my_auctions.php">
            <i class="bi bi-hammer"></i> Mes enchères
          </a>

          <form action="/huhu/huhu_linux/controller/logout.php" method="post">
            <button type="submit" class="dropdown-logout">
              <i class="bi bi-box-arrow-right"></i> Se déconnecter
            </button>
          </form>
        </div>
      </div>

    <?php else: ?>

      <div class="account-actions">
        <a href="/huhu/huhu_linux/views/register_form.php" class="btn-outline">
          S'inscrire
        </a>
        <a href="/huhu/huhu_linux/views/login_form.php" class="connect-btn">
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

  <a href="/huhu/huhu_linux/views/homepage.php">Accueil</a>
  <a href="/huhu/huhu_linux/views/buy_a_horse.php">Enchères</a>

  <?php if ($role === 'organisateur'): ?>
    <a href="/huhu/huhu_linux/views/organisateur_dashboard.php">Gérer</a>
  <?php endif; ?>

  <?php if ($userId): ?>

    <a href="/huhu/huhu_linux/views/profile.php">Mon compte</a>
    <a href="/huhu/huhu_linux/views/my_auctions.php">Mes enchères</a>

    <form action="/huhu/huhu_linux/controller/logout.php" method="post">
      <button type="submit" class="mobile-logout">
        Se déconnecter
      </button>
    </form>

  <?php else: ?>

    <a href="/huhu/huhu_linux/views/register_form.php">S'inscrire</a>
    <a href="/huhu/huhu_linux/views/login_form.php">Se connecter</a>

  <?php endif; ?>
</nav>

<main class="main-content">