<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organisateur') {
    header("Location: homepage.php");
    exit;
}

require_once '../controller/organisateur_auctions_ctrl.php';
require_once '../head.php';

function e($value) {
    return htmlentities($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="ga-page">

    <div class="ga-page-header">
        <h1 class="ga-page-title">Gestion des enchères</h1>
        <p class="ga-page-subtitle">Gérez toutes les enchères</p>
    </div>

    <div class="ga-section">

        <h2>Toutes les enchères (<?= count($auctions) ?>)</h2>

        <?php if (empty($auctions)): ?>

            <p class="ga-empty">Aucune enchère</p>

        <?php else: ?>

        <div class="ga-table-wrapper">

            <table class="ga-table">

                <thead>
                    <tr>
                        <th>Cheval</th>
                        <th>Prix départ</th>
                        <th>Dernière offre</th>
                        <th>Enchérisseur</th>
                        <th>Fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($auctions as $auction): ?>

                    <?php
                    $price   = (float)($auction['auction_starting_price'] ?? 0);
                    $lastBid = (float)($auction['last_bid'] ?? 0);
                    ?>

                    <tr>

                        <td>
                            <?= e($auction['horse_name'] ?? '—') ?>
                            <small>#<?= (int)$auction['horse_id_fk'] ?></small>
                        </td>

                        <td>
                            <?= $price > 0 
                                ? number_format($price, 0, ',', ' ') . ' €'
                                : '-' ?>
                        </td>

                        <td>
                            <?= $lastBid > 0
                                ? number_format($lastBid, 0, ',', ' ') . ' €'
                                : '-' ?>
                        </td>

                        <td>
                            <?= e($auction['last_bidder_name'] ?? '—') ?>
                        </td>

                        <td>
                            <?= !empty($auction['auction_end_date']) 
                                ? date('d/m/Y', strtotime($auction['auction_end_date']))
                                : '-' ?>
                        </td>

                        <td class="ga-actions">

                            <a href="edit_auction.php?id=<?= (int)$auction['id_auction'] ?>" class="ga-btn-edit">
                                Modifier
                            </a>

                            <a href="/huhu/huhu_linux/controller/close_auction.php?id=<?= (int)$auction['id_auction'] ?>" class="ga-btn-close">
                                Clôturer
                            </a>

                            <button
                                type="button"
                                class="ga-btn-delete btn-delete-auction"
                                data-id="<?= (int)$auction['id_auction'] ?>"
                                data-name="<?= e($auction['horse_name']) ?>"
                            >
                                Supprimer
                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php endif; ?>

    </div>

</div>

 <div id="deleteModal" class="custom-modal hidden">

    <div class="modal-card">

        <div class="modal-body mt-3">

            <p>
                Supprimer l’enchère du cheval
                <strong id="deleteAuctionName"></strong> ?
            </p>

            <form action="/huhu/huhu_linux/controller/delete_auction.php" method="POST">

                <input type="hidden" name="auction_id" id="deleteAuctionId">

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <button type="button" class="btn btn-secondary btn-cancel-delete">
                        Annuler
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Supprimer
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
 

<?php require_once '../footer.php'; ?>