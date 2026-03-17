<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../model/config.php';
require_once '../controller/horses_list_ctrl.php';
require_once '../head.php';

function afficherTexteSecurise($valeur) {
    return htmlentities($valeur ?? '', ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$auction_status = $_GET['auction_status'] ?? '';
?>

<div class="hl-page">

    <div class="hl-header">
        <div>
            <h1 class="hl-title">Liste des chevaux</h1>
            <p class="hl-subtitle">Gérez tous les chevaux enregistrés sur la plateforme</p>
        </div>

        <form method="GET" class="hl-filter">
            <select name="auction_status" class="hl-select">
                <option value="">Tous les statuts</option>
                <option value="disponible" <?= $auction_status === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="terminé" <?= $auction_status === 'terminé' ? 'selected' : '' ?>>Terminé</option>
                <option value="annulé" <?= $auction_status === 'annulé' ? 'selected' : '' ?>>Annulé</option>
                <option value="remporté" <?= $auction_status === 'remporté' ? 'selected' : '' ?>>Remporté</option>
            </select>

            <button type="submit" class="hl-btn-filter">
                Filtrer
            </button>
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
                        <td colspan="8" class="hl-empty">
                            Aucun cheval enregistré
                        </td>
                    </tr>

                <?php else: ?>

                <?php foreach ($horses as $horse):

                    $status = $horse['auction_status'] ?? 'indisponible';

                    $badgeMap = [
                        'disponible' => ['hl-badge--disponible', 'Disponible'],
                        'terminé'    => ['hl-badge--termine', 'Terminé'],
                        'annulé'     => ['hl-badge--annule', 'Annulé'],
                        'remporté'   => ['hl-badge--remporte', 'Remporté'],
                    ];

                    [$badgeClass, $label] =
                        $badgeMap[$status] ?? ['hl-badge--indisponible', 'Indisponible'];
                ?>

                <tr>

                    <td data-label="Nom">

                        <div class="hl-horse-cell">

                            <img
                                src="/huhu/huhu_linux/uploads/horses/<?= afficherTexteSecurise($horse['horse_image'] ?? 'horse_default.png') ?>"
                                class="hl-horse-avatar"
                                width="38"
                                height="38"
                                alt=""
                            >

                            <span class="hl-horse-name">
                                <?= afficherTexteSecurise($horse['horse_name']) ?>
                            </span>

                        </div>

                    </td>

                    <td data-label="Sexe">
                        <?= $horse['horse_sex'] === 'M' ? 'Mâle' : 'Femelle' ?>
                    </td>

                    <td data-label="Race">
                        <?= afficherTexteSecurise($horse['horse_breed']) ?>
                    </td>

                    <td data-label="Robe">
                        <?= afficherTexteSecurise($horse['horse_coat']) ?>
                    </td>

                    <td data-label="Discipline">
                        <?= afficherTexteSecurise($horse['horse_discipline']) ?>
                    </td>


                    <td data-label="Statut">

                        <span class="hl-badge <?= $badgeClass ?>">
                            <?= afficherTexteSecurise($label) ?>
                        </span>

                    </td>


                    <td data-label="Gagnant">

                        <?php if ($status === 'terminé' && !empty($horse['winner_name']) && $horse['winner_name'] !== '—'): ?>

                            <span class="hl-winner">
                                🏆 <?= afficherTexteSecurise($horse['winner_name']) ?>
                            </span>

                        <?php elseif ($status === 'terminé'): ?>

                            <span class="hl-no-data">
                                Aucune mise
                            </span>

                        <?php else: ?>

                            <span class="hl-no-data">
                                —
                            </span>

                        <?php endif; ?>

                    </td>


                    <td data-label="Actions">

                        <div class="hl-actions">

                            <!-- VOIR -->
                            <a
                                href="/huhu/huhu_linux/views/horse_info.php?id=<?= (int)$horse['id_horse'] ?>"
                                class="hl-action-btn"
                                title="Voir la fiche"
                            >
                                <i class="bi bi-eye"></i>
                            </a>


                            <!-- MODIFIER -->
                            <a
                                href="/huhu/huhu_linux/views/update_horses_form.php?id=<?= (int)$horse['id_horse'] ?>"
                                class="hl-action-btn"
                                title="Modifier"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>


                            <!-- SUPPRIMER -->
                            <button
                                type="button"
                                class="hl-action-btn hl-action-btn--danger btn-delete-horse"
                                data-id="<?= (int)$horse['id_horse'] ?>"
                                data-name="<?= afficherTexteSecurise($horse['horse_name']) ?>"
                                title="Supprimer"
                            >
                                <i class="bi bi-trash"></i>
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


<!-- DELETE MODAL -->

<div id="deleteModal" class="custom-modal hidden">

    <div class="modal-card">

        <div class="modal-body mt-3">

            <p>
                Êtes-vous sûr de vouloir supprimer
                <strong id="deleteHorseName"></strong> ?
            </p>

            <form action="/huhu/huhu_linux/controller/delete_horses_ctrl.php" method="POST">

                <input type="hidden" name="horse_id" id="deleteHorseId">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= afficherTexteSecurise($_SESSION['csrf_token']) ?>"
                >

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-secondary btn-cancel-delete">
                        Annuler
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Oui, supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>