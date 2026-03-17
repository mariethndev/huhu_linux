<?php
session_start();

require_once '../controller/buy_a_horse_ctrl.php';
require_once '../head.php';

$horses = $horses ?? [];
$count  = $count  ?? 0;

$search     = $_GET['search'] ?? '';
$breed      = $_GET['breed'] ?? '';
$discipline = $_GET['discipline'] ?? '';
$price_min  = $_GET['price_min'] ?? '';
$price_max  = $_GET['price_max'] ?? '';
?>

<div class="bah-page">

    <div class="bah-hero">
        <h1>Liste des annonces</h1>
        <p>Consulte la fiche et décide ensuite si tu veux participer.</p>

        <div class="bah-counter">
            <span class="bah-counter-num"><?= $count ?></span>
            <?= $count > 1 ? 'chevaux correspondent' : 'cheval correspond' ?> à votre recherche
        </div>
    </div>

    <div class="bah-filter-bar">

        <form method="GET" class="bah-filter-form">

            <div class="bah-filter-field">
                <label>Nom</label>
                <input type="text" name="search"
                       value="<?= htmlentities($search) ?>">
            </div>

            <div class="bah-filter-field">
                <label>Sexe</label>
                <select name="filter_sex">
                    <option value="">Tous</option>
                    <option value="jument" <?= ($_GET['filter_sex'] ?? '') === 'jument' ? 'selected' : '' ?>>Femelle</option>
                    <option value="male" <?= ($_GET['filter_sex'] ?? '') === 'male' ? 'selected' : '' ?>>Mâle</option>
                </select>
            </div>

            <div class="bah-filter-field">
                <label>Âge</label>
                <select name="filter_age">
                    <option value="">Tous</option>
                    <option value="poulain" <?= ($_GET['filter_age'] ?? '') === 'poulain' ? 'selected' : '' ?>>Poulain</option>
                    <option value="pouliche" <?= ($_GET['filter_age'] ?? '') === 'pouliche' ? 'selected' : '' ?>>Pouliche</option>
                    <option value="jeune_adulte" <?= ($_GET['filter_age'] ?? '') === 'jeune_adulte' ? 'selected' : '' ?>>Jeune adulte</option>
                    <option value="adulte" <?= ($_GET['filter_age'] ?? '') === 'adulte' ? 'selected' : '' ?>>Adulte</option>
                    <option value="senior" <?= ($_GET['filter_age'] ?? '') === 'senior' ? 'selected' : '' ?>>Senior</option>
                </select>
            </div>

            <div class="bah-filter-field">
                <label>Race</label>
                <input type="text" name="breed"
                       value="<?= htmlentities($breed) ?>">
            </div>

            <div class="bah-filter-field">
                <label>Discipline</label>
                <input type="text" name="discipline"
                       value="<?= htmlentities($discipline) ?>">
            </div>

            <div class="bah-filter-field">
                <label>Prix min</label>
                <input type="number" name="price_min"
                       value="<?= htmlentities($price_min) ?>">
            </div>

            <div class="bah-filter-field">
                <label>Prix max</label>
                <input type="number" name="price_max"
                       value="<?= htmlentities($price_max) ?>">
            </div>

            <div class="bah-filter-actions">
                <button type="submit">Filtrer</button>
                <a href="buy_a_horse.php">Réinitialiser</a>
            </div>

        </form>

    </div>

    <div class="bah-grid">

        <?php if ($count === 0): ?>
            <p>Aucun cheval ne correspond aux filtres.</p>
        <?php endif; ?>

        <?php foreach ($horses as $horse): ?>

            <div class="bah-card">

                <div class="bah-card-image">
                    <img src="/huhu/huhu/uploads/horses/<?= htmlentities($horse['horse_image'] ?? 'horse_default.png') ?>">
                </div>

                <div class="bah-card-body">

                    <h3 class="bah-card-name">
                        <?= htmlentities($horse['horse_name'] ?? 'Inconnu') ?>
                    </h3>

                    <div class="bah-card-meta">
                        <?= htmlentities($horse['horse_breed'] ?? 'Non renseigné') ?>
                    </div>

                    <!-- 🔥 SEXE AJOUTÉ -->
                    <div>
                        Sexe :
                        <?php
                        $sex = $horse['horse_sex'] ?? '';
                        if ($sex === 'M') {
                            echo 'Mâle';
                        } elseif ($sex === 'F') {
                            echo 'Femelle';
                        } else {
                            echo '—';
                        }
                        ?>
                    </div>

                    <div>
                        Discipline :
                        <?= htmlentities($horse['horse_discipline'] ?? '—') ?>
                    </div>

                    <div class="bah-price-value">
                        <?= number_format($horse['current_price'] ?? 0, 0, ',', ' ') ?> €
                    </div>

                    <div class="bah-dates">
                        <strong>Début :</strong>
                        <?= htmlentities($horse['auction_start_date'] ?? '—') ?>
                    </div>
                    
                    <div class="bah-dates">
                        <strong>Fin :</strong>
                        <?= htmlentities($horse['auction_end_date'] ?? '—') ?>
                    </div>

                </div>

                <div class="bah-card-footer">
                    <a href="horse_info.php?id=<?= (int)$horse['id_horse'] ?>" class="bah-btn-consult">
                        Consulter la fiche
                    </a>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require_once '../footer.php'; ?>