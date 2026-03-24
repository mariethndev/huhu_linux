<?php
require_once '../controller/profile_ctrl.php';
require_once '../head.php';

$name     = htmlentities($user['user_name'],  ENT_QUOTES, 'UTF-8');
$email    = htmlentities($user['user_email'], ENT_QUOTES, 'UTF-8');
$role     = htmlentities($user['user_role'],  ENT_QUOTES, 'UTF-8');
$initials = strtoupper(string: mb_substr($user['user_name'], 0, 1, 'UTF-8'));
?>

<div class="pr-page">
    <div class="pr-card">

        <div class="pr-card-top">
            <div class="pr-avatar"><?= $initials ?></div>
            <h1 class="pr-user-name">Modifier mes informations</h1>
            <p class="up-subtitle">Mettez à jour vos informations personnelles</p>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="co-alert co-alert--success">
                    <i class="bi bi-check-circle"></i>
                    Profil mis à jour avec succès.
                </div>
            <?php elseif ($_GET['status'] === 'danger'): ?>
                <div class="co-alert">
                    <i class="bi bi-exclamation-circle"></i>
                    Une erreur est survenue. Veuillez réessayer.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="../controller/update_profile_ctrl.php" method="POST">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= htmlentities($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="up-form-body">

                <div class="co-field">
                    <label class="co-label" for="up_name">Nom</label>
                    <input type="text"
                           name="user_name"
                           id="up_name"
                           class="co-input"
                           value="<?= $name ?>"
                           required>
                </div>

                <div class="co-field">
                    <label class="co-label" for="up_email">Email</label>
                    <input type="email"
                           name="user_email"
                           id="up_email"
                           class="co-input"
                           value="<?= $email ?>"
                           required>
                </div>

                <div class="co-field">
                    <label class="co-label">Rôle</label>
                    <input type="text"
                           class="co-input"
                           value="<?= $role ?>"
                           disabled>
                    <p class="co-hint">Le rôle ne peut pas être modifié</p>
                </div>

            </div>

            <div class="pr-card-footer up-actions">
                <a href="/huhu/huhu_linux/views/profile.php" class="up-btn-cancel">
                    Annuler
                </a>
                <button type="submit" class="co-btn-submit">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>
</div>

<?php require_once '../footer.php'; ?>
