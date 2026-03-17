<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

require_once '../head.php';
?>

<div class="co-page">
    <div class="co-card">

        <div class="co-card-header">
            <div class="co-icon">
                <i class="bi bi-person-plus"></i>
            </div>
            <h1 class="co-title">Créer un organisateur</h1>
            <p class="co-subtitle">Ajoutez un nouveau compte organisateur à la plateforme</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="co-alert">
                <i class="bi bi-exclamation-circle"></i>
                <?php
                    switch ($_GET['error']) {
                        case 'champs':   echo "Veuillez remplir tous les champs."; break;
                        case 'email':    echo "Adresse email invalide."; break;
                        case 'password': echo "Les mots de passe ne correspondent pas."; break;
                        case 'exists':   echo "Cet email est déjà utilisé."; break;
                        default:         echo "Une erreur est survenue.";
                    }
                ?>
            </div>
        <?php endif; ?>

        <form method="post" action="../controller/create_organisateur_ctrl.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <div class="co-field">
                <label class="co-label" for="co_name">Nom complet</label>
                <input type="text"
                       name="name"
                       id="co_name"
                       class="co-input"
                       placeholder="Prénom Nom"
                       value="<?= isset($_GET['name']) ? htmlentities($_GET['name']) : '' ?>"
                       required>
            </div>

            <div class="co-field">
                <label class="co-label" for="co_email">Email</label>
                <input type="email"
                       name="email"
                       id="co_email"
                       class="co-input"
                       placeholder="exemple@email.com"
                       value="<?= isset($_GET['email']) ? htmlentities($_GET['email']) : '' ?>"
                       required>
            </div>

            <div class="co-field">
                <label class="co-label" for="co_password">Mot de passe</label>
                <input type="password"
                       name="password"
                       id="co_password"
                       class="co-input"
                       placeholder="Minimum 8 caractères"
                       required>
                <p class="co-hint">Au moins 8 caractères</p>
            </div>

            <div class="co-field">
                <label class="co-label" for="co_password_confirm">Confirmation du mot de passe</label>
                <input type="password"
                       name="password_confirm"
                       id="co_password_confirm"
                       class="co-input"
                       placeholder="Confirmez le mot de passe"
                       required>
            </div>

            <button type="submit" class="co-btn-submit">
                Créer l'organisateur
            </button>

        </form>

        <a href="/huhu/huhu_linux/views/organisateur_dashboard.php" class="co-back">
            Retour au tableau de bord
        </a>

    </div>
</div>

<?php require_once '../footer.php'; ?>
