<?php
session_start();

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

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="af-alert af-alert--success">
                Modification enregistrée avec succès.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'danger'): ?>
            <div class="af-alert af-alert--danger">
                Une erreur est survenue.
            </div>
        <?php endif; ?>

        <form method="POST" action="../controller/update_auction.php">

            <input type="hidden" name="auction_id" value="<?= (int)$auction['id_auction'] ?>">

            <!-- PRIX -->
            <div class="af-field">
                <label class="af-label">Prix de départ</label>
                <input
                    type="number"
                    name="auction_starting_price"
                    class="af-input"
                    value="<?= htmlentities($auction['auction_starting_price'] ?? 0) ?>"
                    min="0"
                >
            </div>

            <!-- DATE FIN -->
            <div class="af-field">
                <label class="af-label">Date de fin</label>

                <input
                    type="datetime-local"
                    name="auction_end_date"
                    class="af-input"
                    value="<?= !empty($auction['auction_end_date']) ? htmlentities(date('Y-m-d\TH:i', strtotime($auction['auction_end_date']))) : '' ?>"
                    required
                >
            </div>

            <!-- STATUT -->
            <div class="af-field">
                <label class="af-label">Statut</label>

                <select name="auction_status" class="af-select">

                    <option value="disponible"
                        <?= $auction['auction_status'] == 'disponible' ? 'selected' : '' ?>>
                        Disponible
                    </option>

                    <option value="terminé"
                        <?= $auction['auction_status'] == 'terminé' ? 'selected' : '' ?>>
                        Terminé
                    </option>

                    <option value="annulé"
                        <?= $auction['auction_status'] == 'annulé' ? 'selected' : '' ?>>
                        Annulé
                    </option>

                </select>
            </div>

            <div class="af-footer">
                <button type="submit" class="btn btn-dark btn-md">
                    Enregistrer
                </button>
            </div>

        </form>

        <div class="af-divider"></div>

        <form method="POST" action="../controller/close_auction.php">

            <input type="hidden" name="auction_id" value="<?= (int)$auction['id_auction'] ?>">

            <div class="af-footer">

                <button type="submit" class="btn btn-secondary btn-md">
                    Clôturer maintenant
                </button>

            </div>

        </form>

    </div>

</div>

<?php require_once '../footer.php'; ?>