<?php
session_start();

// Redirige les utilisateurs non organisateurs vers la page d'accueil
// Si l'utilisateur n'est pas connecté ou n'est pas un organisateur, redirige vers la page d'accueil
// Cette vérification empêche les utilisateurs non autorisés d'accéder à la page de modification des enchères
// L'utilisation de $_SESSION['role'] permet de vérifier le rôle de l'utilisateur connecté
//  Si l'utilisateur n'est pas un organisateur, il est redirigé vers "homepage.php" et le script s'arrête avec exit
// Cela garantit que seuls les organisateurs peuvent accéder à cette page et effectuer des modifications sur les enchères

if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: homepage.php");
    exit;
}

require_once '../controller/update_auction.php';
require_once '../head.php';

?>

<div class="af-page">

    <div class="af-page-header">
        <h1 class="af-page-title">Modifier l'enchère</h1>
        <p class="af-page-subtitle">Gérez les paramètres de l'enchère.</p>
    </div>

    <div class="af-card">

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="af-alert af-alert--success">
                Modification enregistrée avec succès.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'danger'): ?>
            <div class="af-alert af-alert--danger">
                Une erreur est survenue lors de la modification.
            </div>
        <?php endif; ?>

        <form method="POST" action="../controller/update_auction.php">

            <input type="hidden" name="auction_id" value="<?= $auction['id_auction'] ?>">

            <div class="af-field">

                <label class="af-label">
                    Date de fin
                </label>

                <input
                    type="date"
                    name="auction_end_date"
                    class="af-input"
                    value="<?= htmlentities($dateValue) ?>"
                    required
                >

            </div>

            <div class="af-field">

                <label class="af-label">
                    Statut
                </label>

                <select name="auction_status" class="af-select">

                    <option value="disponible" <?= $auction['auction_status'] === 'disponible' ? 'selected' : '' ?>>
                        Disponible
                    </option>

                    <option value="terminé" <?= $auction['auction_status'] === 'terminé' ? 'selected' : '' ?>>
                        Terminé
                    </option>

                    <option value="annulé" <?= $auction['auction_status'] === 'annulé' ? 'selected' : '' ?>>
                        Annulé
                    </option>

                </select>

            </div>

            <div class="af-footer">

                <button type="submit" class="btn btn-dark btn-md">
                    Enregistrer les modifications
                </button>

            </div>

        </form>

        <div class="af-divider"></div>

        <form method="POST" action="../controller/close_auction.php">

            <input type="hidden" name="auction_id" value="<?= $auction['id_auction'] ?>">

            <div class="af-footer">

                <button type="submit" class="btn btn-secondary btn-md">
                    Clôturer l'enchère maintenant
                </button>

            </div>

        </form>

    </div>

</div>

<?php require_once '../footer.php'; ?>