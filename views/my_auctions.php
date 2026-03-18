<?php
session_start();
require_once '../controller/my_auctions_ctrl.php';
require_once '../head.php';

function e($v) {
    return htmlentities($v ?? '', ENT_QUOTES, 'UTF-8');
}

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

     <?php if (!empty($outbidCount) && $outbidCount > 0): ?>
        <div class="alert alert-warning">
            🔔 Vous avez été dépassé sur 
            <strong><?= $outbidCount ?></strong> enchère<?= $outbidCount > 1 ? 's' : '' ?> :

            <?php if ($outbidCount === 1): ?>
                <a href="/huhu/huhu_linux/views/horse_info.php?id=<?= (int)$outbids[0]['horse_id_fk'] ?>">
                    Voir <?= e($outbids[0]['horse_name']) ?>
                </a>
            <?php else: ?>
                <a href="#en_cours">Voir les enchères concernées</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($hasAuctions): ?>

         <?php if (!empty($groupedAuctions['en_cours'])): ?>
            <h2 id="en_cours">En cours</h2>

            <table class="hl-table">
                <thead>
                    <tr>
                        <th>Cheval</th>
                        <th>Prix actuel</th>
                        <th>Mon enchère</th>
                        <th>Statut</th>
                        <th>Participants</th>
                        <th>Fin</th>
                        <th>Dernier enchérisseur</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($groupedAuctions['en_cours'] as $a): ?>
                        <tr>
                            <td><?= e($a['horse_name']) ?></td>

                            <td><?= number_format((float)$a['last_price'], 0, ',', ' ') ?> €</td>

                            <td><?= number_format((float)$a['my_last_bid'], 0, ',', ' ') ?> €</td>

                            <td>
                                <?php if (!empty($a['is_outbid'])): ?>
                                    <span class="status outbid">Surenchéri</span>
                                <?php else: ?>
                                    <span class="status leading">En tête</span>
                                <?php endif; ?>
                            </td>

                            <td><?= (int)$a['participants'] ?></td>

                            <td><?= !empty($a['auction_end_date']) ? date('d/m/Y H:i', strtotime($a['auction_end_date'])) : '—' ?></td>

                            <td>
                                <?php
                                $sessionName = $_SESSION['user_name'] ?? '';
                                echo ($a['last_bidder'] === $sessionName)
                                    ? 'Toi'
                                    : e($a['last_bidder'] ?? '—');
                                ?>
                            </td>

                            <td>
                                <a href="horse_info.php?id=<?= (int)$a['id_horse'] ?>" class="btn-consult">
                                    Voir
                                </a>
                            </td>
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
                            <td><?= e($a['horse_name']) ?></td>
                            <td><?= number_format((float)$a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= number_format((float)$a['my_last_bid'], 0, ',', ' ') ?> €</td>
                            <td><?= (int)$a['participants'] ?></td>
                            <td><?= !empty($a['auction_end_date']) ? date('d/m/Y H:i', strtotime($a['auction_end_date'])) : '—' ?></td>
                            <td><?= e($a['last_bidder'] ?? 'Aucun') ?></td>
                            <td>
                                <a href="horse_info.php?id=<?= (int)$a['id_horse'] ?>">Voir</a>
                            </td>
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
                            <td><?= e($a['horse_name']) ?></td>
                            <td><?= number_format((float)$a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= number_format((float)$a['my_last_bid'], 0, ',', ' ') ?> €</td>
                            <td><?= (int)$a['participants'] ?></td>
                            <td><?= !empty($a['auction_end_date']) ? date('d/m/Y H:i', strtotime($a['auction_end_date'])) : '—' ?></td>
                            <td><?= e($a['last_bidder'] ?? 'Aucun') ?></td>
                            <td>
                                <a href="horse_info.php?id=<?= (int)$a['id_horse'] ?>">Voir</a>
                            </td>
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
                            <td><?= e($a['horse_name']) ?></td>
                            <td><?= number_format((float)$a['last_price'], 0, ',', ' ') ?> €</td>
                            <td><?= number_format((float)$a['my_last_bid'], 0, ',', ' ') ?> €</td>
                            <td><?= (int)$a['participants'] ?></td>
                            <td><?= !empty($a['auction_end_date']) ? date('d/m/Y H:i', strtotime($a['auction_end_date'])) : '—' ?></td>
                            <td><?= e($a['last_bidder'] ?? 'Aucun') ?></td>
                            <td>
                                <a href="horse_info.php?id=<?= (int)$a['id_horse'] ?>">Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php else: ?>

        <div class="ma-empty-state">
            <p>Vous ne participez à aucune enchère pour le moment.</p>
            <a href="buy_a_horse.php" class="btn-consult">
                Découvrir les enchères
            </a>
        </div>

    <?php endif; ?>

</div>

<?php require_once '../footer.php'; ?>