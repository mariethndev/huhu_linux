<?php 
require_once '../head.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>

<div class="signup-page">
  <div class="signup-form-wrapper">

    <h2>Se connecter</h2>

    <p class="signup-subtitle">
      Si vous n'avez pas de compte,
      <a href="register_form.php">Inscrivez-vous</a>
    </p>

    <?php if (isset($_GET['message'])): ?>
      <div class="alert alert-<?= htmlentities($_GET['status'] ?? 'danger'); ?>">
        <?= htmlentities($_GET['message']); ?>
      </div>
    <?php endif; ?>

    <form action="../controller/login_ctrl.php" method="post">

       <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

      <div class="signup-group">
        <label for="mail">Email</label>
        <input 
          type="email" 
          id="mail"
          name="mail" 
          required
        >
      </div>

      <div class="signup-group">
        <label for="psw">Mot de passe</label>
        <div class="password-wrapper">
          <input 
            type="password" 
            id="psw"
            name="psw" 
            required
          >
          <button type="button" class="password-toggle">🙈</button>
        </div>
      </div>

      <button type="submit" class="signup-btn">
        Se connecter
      </button>

    </form>

  </div>
</div>

<?php require_once '../footer.php'; ?>