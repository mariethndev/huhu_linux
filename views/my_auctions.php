<?php
session_start();
require_once '../controller/my_auctions_ctrl.php';
require_once '../head.php';

 $hasAuctions = 
    !empty($groupedAuctions['en_cours']) ||
    !empty($groupedAuctions['annulees']) ||
    !empty($groupedAuctions['terminees']) ||
    !empty($groupedAuctions['remportees']);
?>

<div class="hl-page">
    <div class="hl-header">
        <h1 class="hl-title">Mes enchères</h1>
        <p class="hl-subtitle">Chevaux sur lesquels vous avez enchéri</p>
    </div>

     <?php if ($outbidCount > 0): ?>
        <div style="background:#ffe0e0;color:#900;padding:10px;border-radius:8px;margin-bottom:20px;">
            Vous avez été dépassé sur <?= $outbidCount ?> enchère(s)
        </div>
    <?php endif; ?>

    <?php if ($hasAuctions): ?>

         <?php if (!empty($groupedAuctions['en_cours'])): ?>
            <h2>En cours</h2>
            <table class="hl-table">
                <tbody>
                    <?php foreach ($groupedAuctions['en_cours'] as $a): ?>
                        <tr>
                            <td><?= htmlentities($a['horse_name']) ?></td>
                            <td><?= number_format($a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= $a['participants'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['auction_end_date'])) ?></td>
                            <td><a href="horse_info.php?id=<?= $a['id_horse'] ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

         <?php if (!empty($groupedAuctions['annulees'])): ?>
            <h2>Annulées</h2>
            <table class="hl-table">
                <tbody>
                    <?php foreach ($groupedAuctions['annulees'] as $a): ?>
                        <tr>
                            <td><?= htmlentities($a['horse_name']) ?></td>
                            <td><?= number_format($a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= $a['participants'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['auction_end_date'])) ?></td>
                            <td><a href="horse_info.php?id=<?= $a['id_horse'] ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

         <?php if (!empty($groupedAuctions['terminees'])): ?>
            <h2>Terminées</h2>
            <table class="hl-table">
                <tbody>
                    <?php foreach ($groupedAuctions['terminees'] as $a): ?>
                        <tr>
                            <td><?= htmlentities($a['horse_name']) ?></td>
                            <td><?= number_format($a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= $a['participants'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['auction_end_date'])) ?></td>
                            <td><a href="horse_info.php?id=<?= $a['id_horse'] ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

         <?php if (!empty($groupedAuctions['remportees'])): ?>
            <h2>Remportées</h2>
            <table class="hl-table">
                <tbody>
                    <?php foreach ($groupedAuctions['remportees'] as $a): ?>
                        <tr>
                            <td><?= htmlentities($a['horse_name']) ?></td>
                            <td><?= number_format($a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= $a['participants'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['auction_end_date'])) ?></td>
                            <td><a href="horse_info.php?id=<?= $a['id_horse'] ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php else: ?>

         <div class="ma-empty-state">
            <p>Vous ne participez à aucune enchère pour le moment.</p>
            <a href="buy_a_horse.php">Découvrir les enchères</a>
        </div>

    <?php endif; ?>

</div>

<?php require_once '../footer.php'; ?>