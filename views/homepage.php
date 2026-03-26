<?php
session_start();

require_once '../controller/homepage_ctrl.php';
require_once '../head.php';

$horses = $horses ?? [];
$count = $count ?? count($horses);
?>

<section class="collection-section">

    <div class="collection-container">

        <div class="collection-head">
            <h2 class="collection-title">Enchères récentes</h2>
            <p class="collection-desc">
                Vous avez trouvé le cheval idéal ? Achetez ou vendez rapidement et en toute sécurité.
            </p>
        </div>

        <?php if ($count === 0): ?>

            <p class="collection-empty">
                Aucune enchère disponible pour le moment.
            </p>

        <?php else: ?>

        <div class="collection-grid">

            <?php foreach ($horses as $horse): ?>

                <?php
                // je récupère l'image depuis la BDD ou défaut
                $image = $horse['horse_image'] ?? 'horse_default.png';

                // je vérifie si le fichier existe vraiment
                $filePath = __DIR__ . "/../uploads/horses/" . $image;

                if (!file_exists($filePath)) {
                    $image = "horse_default.png";
                }

                // je construis le chemin web
                $imagePath = "/huhu/huhu_linux/uploads/horses/" . $image;
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
                            Prix actuel :
                            <?php if ($horse['current_price'] !== null): ?>
                                <?= number_format($horse['current_price'], 0, ',', ' ') ?> €
                            <?php else: ?>
                                Aucune enchère
                            <?php endif; ?>
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