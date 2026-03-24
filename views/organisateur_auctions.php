<?php
session_start();

if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: homepage.php");
    exit;
}

require_once '../controller/organisateur_auctions_ctrl.php';
require_once '../head.php';

function escapeHtml($value) {
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

                <?php foreach ($auctions as $auctionItem): ?>

                    <?php
                    $startPrice = (float)($auctionItem['auction_starting_price'] ?? 0);
                    $lastPrice  = (float)($auctionItem['last_bid'] ?? 0);
                    ?>

                    <tr>

                        <td>
                            <?= escapeHtml($auctionItem['horse_name'] ?? '—') ?>
                            <small>#<?= (int)$auctionItem['horse_id_fk'] ?></small>
                        </td>

                        <td>
                            <?php if ($startPrice > 0): ?>
                                <?= number_format($startPrice, 0, ',', ' ') ?> €
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($lastPrice > 0): ?>
                                <?= number_format($lastPrice, 0, ',', ' ') ?> €
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= escapeHtml($auctionItem['last_bidder_name'] ?? '—') ?>
                        </td>

                        <td>
                            <?= !empty($auctionItem['auction_end_date'])
                                ? date('d/m/Y', strtotime($auctionItem['auction_end_date']))
                                : '-' ?>
                        </td>

                        <td class="ga-actions">

                            <a href="edit_auction.php?id=<?= (int)$auctionItem['id_auction'] ?>" class="ga-btn-edit">
                                Modifier
                            </a>

                            <button
                                type="button"
                                class="ga-btn-delete btn-delete-auction btn btn-danger"
                                data-id="<?= (int)$auctionItem['id_auction'] ?>"
                                data-name="<?= escapeHtml($auctionItem['horse_name']) ?>"
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