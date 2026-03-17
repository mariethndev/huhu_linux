<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organisateur') {
    header("Location: homepage.php");
    exit;
}

require_once '../controller/organisateur_auctions_ctrl.php';
require_once '../head.php';

function renderSection($rows, $title)
{
    $count = count($rows);
?>

<div class="ga-section">

    <h2><?php echo htmlentities($title); ?> (<?php echo $count; ?>)</h2>

    <?php if ($count === 0): ?>

        <p class="ga-empty">Aucune enchère dans cette catégorie.</p>

    <?php else: ?>

        <div class="ga-table-wrapper">

            <table class="ga-table table-auctions-mobile">

                <thead>
                    <tr>
                        <th>Cheval</th>
                        <th>Prix départ</th>
                        <th>Dernière offre</th>
                        <th>Dernier enchérisseur</th>
                        <th>Date de fin</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($rows as $auction): ?>

                    <tr>

                        <td data-label="Cheval">
                            <?php
                            $name = $auction['horse_name'] ?? '—';
                            echo htmlentities($name);
                            ?>
                        </td>

                        <td data-label="Prix départ">
                            <?php
                            $price = $auction['auction_starting_price'] ?? 0;
                            echo number_format($price, 0, ',', ' ') . ' €';
                            ?>
                        </td>

                        <td data-label="Dernière offre">
                            <?php
                            if (!empty($auction['last_bid'])) {
                                echo number_format($auction['last_bid'], 0, ',', ' ') . " €";
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>

                        <td data-label="Dernier enchérisseur">
                            <?php
                            if (!empty($auction['last_bidder'])) {
                                echo htmlentities($auction['last_bidder']);
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>

                        <td data-label="Date de fin">
                            <?php
                            if (!empty($auction['auction_end_date'])) {
                                echo date('d/m/Y', strtotime($auction['auction_end_date']));
                            } else {
                                echo '—';
                            }
                            ?>
                        </td>

                        <td data-label="Statut">
                            <?php
                            $status = $auction['auction_status'] ?? 'disponible';
                            echo htmlentities($status);
                            ?>
                        </td>

                        <td data-label="Action">
                            <?php
                            $id = $auction['id_auction'] ?? 0;
                            ?>
                            <a class="ga-btn-edit" href="edit_auction.php?id=<?php echo (int)$id; ?>">
                                Modifier
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<?php
}
?>

<div class="ga-page">

    <div class="ga-page-header">
        <h1 class="ga-page-title">Gestion des enchères</h1>
        <p class="ga-page-subtitle">Suivez toutes vos enchères.</p>
    </div>

    <?php renderSection($enCours, "En cours"); ?>
    <?php renderSection($terminees, "Terminées"); ?>
    <?php renderSection($annulees, "Annulées"); ?>

</div>

<?php require_once '../footer.php'; ?>