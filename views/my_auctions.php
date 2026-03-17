<?php
session_start();
require_once '../controller/my_auctions_ctrl.php';
require_once '../head.php';
?>

<div class="hl-page">
    <div class="hl-header">
        <h1 class="hl-title">Mes enchères</h1>
        <p class="hl-subtitle">Chevaux sur lesquels vous avez enchéri</p>
    </div>

    <?php if (empty($auctions)): ?>
        <div class="ma-empty-state">
            <p>Vous ne participez à aucune enchère pour le moment.</p>
            <a href="buy_a_horse.php">Découvrir les enchères</a>
        </div>
    <?php else: ?>
        <table class="hl-table">
            <thead>
                <tr>
                    <th>Cheval</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auctions as $a): ?>
                    <tr>
                        <td><?= htmlentities($a['horse_name'] ?? '—') ?></td>
                        <td>
                            <a href="horse_info.php?id=<?= (int)$a['id_horse'] ?>">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once '../footer.php'; ?>