<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../model/config.php';
require_once '../controller/horses_list_ctrl.php';
require_once '../head.php';

// je génère le token csrf
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$auction_status = $_GET['auction_status'] ?? '';
?>

<div class="hl-page">

    <div class="hl-header">
        <div>
            <h1 class="hl-title">Liste des chevaux</h1>
            <p class="hl-subtitle">Gérez les chevaux</p>
        </div>

        <form method="GET" class="hl-filter">
            <select name="auction_status" class="hl-select">

                <option value="">Tous les statuts</option>

                <option value="disponible" <?= $auction_status == 'disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="terminé" <?= $auction_status == 'terminé' ? 'selected' : '' ?>>Terminé</option>
                <option value="annulé" <?= $auction_status == 'annulé' ? 'selected' : '' ?>>Annulé</option>
                <option value="indisponible" <?= $auction_status == 'indisponible' ? 'selected' : '' ?>>Indisponible</option>

            </select>

            <button type="submit" class="hl-btn-filter">Filtrer</button>
        </form>
    </div>

    <div class="hl-container">
        <div class="hl-table-wrapper">

            <table class="hl-table">

                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Sexe</th>
                        <th>Race</th>
                        <th>Robe</th>
                        <th>Discipline</th>
                        <th>Statut</th>
                        <th>Gagnant</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($horses)): ?>

                    <tr>
                        <td colspan="8" class="hl-empty">Aucun cheval</td>
                    </tr>

                <?php else: ?>

                <?php foreach ($horses as $horse): ?>

                    <?php
                    $status = $horse['horse_status'] ?? 'indisponible';

                    switch ($status) {
                        case 'disponible':
                            $badgeClass = 'hl-badge--disponible';
                            $label = 'Disponible';
                            break;
                        case 'terminé':
                            $badgeClass = 'hl-badge--termine';
                            $label = 'Terminé';
                            break;
                        case 'annulé':
                            $badgeClass = 'hl-badge--annule';
                            $label = 'Annulé';
                            break;
                        default:
                            $badgeClass = 'hl-badge--indisponible';
                            $label = 'Indisponible';
                    }

                    // je gère l'image avec fallback
                    $image = $horse['horse_image'] ?? 'horse_default.png';

                    $filePath = __DIR__ . "/../uploads/horses/" . $image;

                    if (!file_exists($filePath)) {
                        $image = "horse_default.png";
                    }

                    $imagePath = "/huhu/huhu_linux/uploads/horses/" . $image;
                    ?>

                <tr>

                    <td>
                        <div class="hl-horse-cell">

                            <img
                                src="<?= $imagePath ?>"
                                class="hl-horse-avatar"
                                width="38"
                                height="38"
                            >

                            <span class="hl-horse-name">
                                <?= $horse['horse_name'] ?? '—' ?>
                            </span>

                        </div>
                    </td>

                    <td>
                        <?= ($horse['horse_sex'] ?? '') === 'M' ? 'Mâle' : 'Femelle' ?>
                    </td>

                    <td><?= $horse['horse_breed'] ?? '—' ?></td>
                    <td><?= $horse['horse_coat'] ?? '—' ?></td>
                    <td><?= $horse['horse_discipline'] ?? '—' ?></td>

                    <td>
                        <span class="hl-badge <?= $badgeClass ?>">
                            <?= $label ?>
                        </span>
                    </td>

                    <td>
                        <?php if ($status === 'terminé'): ?>

                            <?php if (!empty($horse['winner_name']) && $horse['winner_name'] !== '—'): ?>
                                <span class="hl-winner">
                                    🏆 <?= $horse['winner_name'] ?>
                                </span>
                            <?php else: ?>
                                <span class="hl-no-data">Aucune mise</span>
                            <?php endif; ?>

                        <?php else: ?>
                            <span class="hl-no-data">—</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div class="hl-actions">

                            <a href="/huhu/huhu_linux/views/horse_info.php?id=<?= (int)$horse['id_horse'] ?>">
                                👁
                            </a>

                            <a href="/huhu/huhu_linux/views/update_horses_form.php?id=<?= (int)$horse['id_horse'] ?>">
                                ✏️
                            </a>

                            <button
                                type="button"
                                class="hl-action-btn hl-action-btn--danger btn-delete-horse"
                                data-id="<?= (int)$horse['id_horse'] ?>"
                                data-name="<?= $horse['horse_name'] ?? '' ?>"
                            >
                                🗑
                            </button>

                        </div>
                    </td>

                </tr>

                <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>
</div>

<div id="deleteModal" class="custom-modal hidden">
    <div class="modal-card">
        <div class="modal-body mt-3">

            <p>
                Supprimer <strong id="deleteHorseName"></strong> ?
            </p>

            <form action="/huhu/huhu_linux/controller/delete_horses_ctrl.php" method="POST">

                <input type="hidden" name="horse_id" id="deleteHorseId">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= $_SESSION['csrf_token'] ?>">

                <button type="button" class="btn btn-secondary btn-cancel-delete">
                    Annuler
                </button>

                <button type="submit" class="btn btn-danger">
                    Supprimer
                </button>

            </form>

        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>