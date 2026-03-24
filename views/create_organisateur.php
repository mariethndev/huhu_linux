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
            <p class="co-subtitle">
                Ajoutez un nouveau compte organisateur
            </p>
        </div>

         <?php if (isset($_GET['error'])): ?>

            <div class="co-alert">

                <i class="bi bi-exclamation-circle"></i>

                <?php
                $error = $_GET['error'];

                if ($error == 'champs') {
                    echo "Veuillez remplir tous les champs.";
                } elseif ($error == 'email') {
                    echo "Adresse email invalide.";
                } elseif ($error == 'password') {
                    echo "Les mots de passe ne correspondent pas.";
                } elseif ($error == 'exists') {
                    echo "Cet email est déjà utilisé.";
                } else {
                    echo "Une erreur est survenue.";
                }
                ?>

            </div>

        <?php endif; ?>

         <form method="post" action="../controller/create_organisateur_ctrl.php">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

             <div class="co-field">
                <label class="co-label">Nom complet</label>

                <input type="text"
                       name="name"
                       class="co-input"
                       placeholder="Prénom Nom"
                       value="<?= isset($_GET['name']) ? htmlentities($_GET['name']) : '' ?>"
                       required>
            </div>

             <div class="co-field">
                <label class="co-label">Email</label>

                <input type="email"
                       name="email"
                       class="co-input"
                       placeholder="exemple@email.com"
                       value="<?= isset($_GET['email']) ? htmlentities($_GET['email']) : '' ?>"
                       required>
            </div>

             <div class="co-field">
                <label class="co-label">Mot de passe</label>

                <input type="password"
                       name="password"
                       class="co-input"
                       placeholder="Minimum 8 caractères"
                       required>

                <p class="co-hint">Au moins 8 caractères</p>
            </div>

             <div class="co-field">
                <label class="co-label">Confirmation du mot de passe</label>

                <input type="password"
                       name="password_confirm"
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