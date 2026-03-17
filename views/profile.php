<?php
require_once '../controller/profile_ctrl.php';
require_once '../head.php';

$roleLabels = [
    'visiteur'     => 'Visiteur',
    'acheteur'     => 'Acheteur',
    'vendeur'      => 'Vendeur',
    'organisateur' => 'Organisateur',
];

$name  = htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8');
$email = htmlentities($user['user_email'], ENT_QUOTES, 'UTF-8');

$role = $roleLabels[$user['user_role']] ?? 'Inconnu';

$initials = strtoupper(substr($user['user_name'], 0, 1));
?>

<div class="pr-page">
    <div class="pr-card">

        <div class="pr-card-top">
            <div class="pr-avatar"><?= $initials ?></div>
            <h1 class="pr-user-name"><?= $name ?></h1>
            <span class="pr-role-badge"><?= $role ?></span>
        </div>

        <div class="pr-info-list">

            <div class="pr-info-item">
                <div class="pr-info-icon">
                    <i class="bi bi-person"></i>
                </div>
                <div class="pr-info-content">
                    <div class="pr-info-label">Nom</div>
                    <div class="pr-info-value"><?= $name ?></div>
                </div>
            </div>

            <div class="pr-info-item">
                <div class="pr-info-icon">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="pr-info-content">
                    <div class="pr-info-label">Email</div>
                    <div class="pr-info-value"><?= $email ?></div>
                </div>
            </div>

            <div class="pr-info-item">
                <div class="pr-info-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="pr-info-content">
                    <div class="pr-info-label">Rôle</div>
                    <div class="pr-info-value"><?= $role ?></div>
                </div>
            </div>

        </div>

        <div class="pr-card-footer">
            <a href="/huhu/huhu/views/update_profile.php" class="pr-btn-edit">
                <i class="bi bi-pencil"></i>
                Modifier mes informations
            </a>
        </div>

    </div>
</div>

<?php require_once '../footer.php'; ?>
