<?php
session_start();

require_once '../controller/homepage_ctrl.php';
require_once '../head.php';

$horses = $horses ?? [];
$count  = $count ?? count($horses);
?>

<section class="collection-section">

    <div class="collection-container">

        <div class="collection-head">
            <h2 class="collection-title">Enchères récentes</h2>
            <p class="collection-desc">Vous avez trouvé le cheval idéal ? Achetez ou vendez rapidement et en toute sécurité.</p>
        </div>

        <?php if ($count === 0): ?>
            <p class="collection-empty">Aucune enchère disponible pour le moment.</p>
        <?php else: ?>

        <div class="collection-grid">

            <?php foreach ($horses as $horse):
                $imagePath = "/huhu/huhu/uploads/horses/" .
                    (!empty($horse['horse_image'])
                        ? htmlentities($horse['horse_image'])
                        : "horse_default.png");
            ?>

            <a href="horse_info.php?id=<?= $horse['id_horse'] ?>" class="collection-card">
                <div class="collection-image">
                    <img src="<?= $imagePath ?>" alt="<?= htmlentities($horse['horse_name']) ?>">
                    <div class="collection-badge">
                        <span class="badge-link">
                            <?= htmlentities($horse['horse_name']) ?> – Voir sa fiche
                        </span>
                    </div>
                    <div class="collection-cta">
                        Prix actuel : <?= number_format($horse['current_price'] ?? 0, 0, ',', ' ') ?> €
                    </div>
                </div>
            </a>

            <?php endforeach; ?>

        </div>

        <?php endif; ?>

        <div class="collection-footer">
            <a href="buy_a_horse.php" class="collection-btn">
                Voir toutes les enchères
            </a>
        </div>

    </div>

</section>

<?php require_once '../footer.php'; ?>
