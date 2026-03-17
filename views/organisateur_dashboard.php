<?php
session_start();

if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: homepage.php");
    exit;
}

require_once '../head.php';
?>

<div class="dash-page">

    <div class="dash-header">
        <h1 class="dash-title">Tableau de bord</h1>
        <p class="dash-subtitle">Gérez votre plateforme depuis un seul endroit</p>
    </div>

    <div class="dash-grid">

        <a href="/huhu/huhu_linux/views/add_horses_form.php" class="dash-card">
            <div class="dash-card-icon">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="dash-card-content">
                <h2 class="dash-card-title">Ajouter un cheval</h2>
                <p class="dash-card-desc">Enregistrez un nouveau cheval sur la plateforme</p>
            </div>
            <span class="dash-card-cta">Accéder</span>
        </a>

        <a href="/huhu/huhu_linux/views/horses_list.php" class="dash-card">
            <div class="dash-card-icon">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="dash-card-content">
                <h2 class="dash-card-title">Gérer les chevaux</h2>
                <p class="dash-card-desc">Modifiez ou supprimez les fiches existantes</p>
            </div>
            <span class="dash-card-cta">Accéder</span>
        </a>

        <a href="/huhu/huhu_linux/views/create_organisateur.php" class="dash-card">
            <div class="dash-card-icon">
                <i class="bi bi-person-plus"></i>
            </div>
            <div class="dash-card-content">
                <h2 class="dash-card-title">Créer un organisateur</h2>
                <p class="dash-card-desc">Ajoutez un nouveau compte organisateur</p>
            </div>
            <span class="dash-card-cta">Accéder</span>
        </a>

        <a href="/huhu/huhu_linux/views/organisateur_auctions.php" class="dash-card">
            <div class="dash-card-icon">
                <i class="bi bi-hammer"></i>
            </div>
            <div class="dash-card-content">
                <h2 class="dash-card-title">Gérer les enchères</h2>
                <p class="dash-card-desc">Suivez et administrez toutes les enchères en cours</p>
            </div>
            <span class="dash-card-cta">Accéder</span>
        </a>

    </div>

</div>

<?php require_once '../footer.php'; ?>
