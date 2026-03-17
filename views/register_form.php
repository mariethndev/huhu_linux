<?php 
include_once '../head.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="signup-page">
    <div class="signup-form-wrapper">
        <h2>Créer un compte</h2>
        <p class="signup-subtitle">
            Si vous avez déjà un compte,
            <a href="login_form.php">connectez-vous</a>
        </p>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-<?= htmlentities($_GET['status'] ?? 'danger'); ?>">
                <?= htmlentities($_GET['message']); ?>
            </div>
        <?php endif; ?>
        
        <form action="../controller/register_ctrl.php" method="post">

             <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="mail" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" name="psw" required>
                    <button type="button" class="password-toggle">🙊</button>
                </div>
            </div>
            
            <div class="profil-group">
                <label>Profil</label>
                <div class="radio-options">
                    <label>
                        <input type="radio" name="profil" value="acheteur" required> Acheteur
                    </label>
                    <label>
                        <input type="radio" name="profil" value="vendeur" required> Vendeur
                    </label>
                </div>
            </div>
            
            <button type="submit" class="signup-btn">
                S'inscrire
            </button>

        </form>
    </div>
</div>

<?php include_once '../footer.php'; ?>