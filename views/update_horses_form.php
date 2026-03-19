<?php
session_start();

require_once "../model/config.php";
require_once '../head.php';

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

$horseId = (int)($_GET['id'] ?? 0);

if ($horseId <= 0) {
    header("Location: ../views/horses_list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM horses WHERE id_horse = ?");
$stmt->execute([$horseId]);

$horse = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$horse) {
    header("Location: ../views/horses_list.php");
    exit;
}

$imagePath = !empty($horse['horse_image'])
    ? "/huhu/huhu_linux/uploads/horses/" . $horse['horse_image']
    : "/huhu/huhu_linux/uploads/horses/horse_default.png";
?>

<div class="af-page">

    <div class="af-page-header">
        <h1 class="af-page-title">Modifier le cheval</h1>
        <p class="af-page-subtitle">
            Modifiez les informations du cheval.
        </p>
    </div>

     <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="af-alert af-alert--success">
            Cheval mis à jour avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'danger'): ?>
        <div class="af-alert af-alert--danger">
            Une erreur est survenue.
        </div>
    <?php endif; ?>

    <div class="af-card">

        <form action="../controller/update_horses_ctrl.php"
              method="post"
              enctype="multipart/form-data">

            <input type="hidden"
                   name="horse_id"
                   value="<?= (int)($horse['id_horse'] ?? 0) ?>">

            <div class="af-grid-2">

                 <div class="af-field af-field--full">
                    <label class="af-label">Photo du cheval</label>

                    <div style="margin-bottom:10px">
                        <img
                            id="horsePreview"
                            src="<?= $imagePath ?>"
                            style="max-width:250px;border-radius:8px;">
                    </div>

                    <input type="file"
                           name="horse_image"
                           id="horseImageInput"
                           accept="image/*">
                </div>

                 <div class="af-field">
                    <label>Nom *</label>
                    <input type="text"
                           name="name"
                           value="<?= htmlentities($horse['horse_name'] ?? '') ?>"
                           required>
                </div>

                 <div class="af-field">
                    <label>Sexe *</label>
                    <select name="sex" required>

                        <option value="M"
                            <?= ($horse['horse_sex'] ?? '') == 'M' ? 'selected' : '' ?>>
                            Mâle
                        </option>

                        <option value="F"
                            <?= ($horse['horse_sex'] ?? '') == 'F' ? 'selected' : '' ?>>
                            Femelle
                        </option>

                    </select>
                </div>

                 <div class="af-field">
                    <label>Date de naissance *</label>
                    <input type="date"
                           name="birthdate"
                           value="<?= htmlentities($horse['horse_birthdate'] ?? '') ?>"
                           required>
                </div>

                 <div class="af-field">
                    <label>Race *</label>
                    <input type="text"
                           name="race"
                           value="<?= htmlentities($horse['horse_breed'] ?? '') ?>"
                           required>
                </div>

                 <div class="af-field">
                    <label>Discipline</label>
                    <input type="text"
                           name="discipline"
                           value="<?= htmlentities($horse['horse_discipline'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Robe</label>
                    <input type="text"
                           name="coat"
                           value="<?= htmlentities($horse['horse_coat'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Taille (cm)</label>
                    <input type="number"
                           name="height"
                           value="<?= htmlentities($horse['horse_height'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Poids (kg)</label>
                    <input type="number"
                           name="weight"
                           value="<?= htmlentities($horse['horse_weight'] ?? '') ?>">
                </div>

                 <div class="af-field af-field--full">
                    <label>Lieu</label>
                    <input type="text"
                           name="location"
                           value="<?= htmlentities($horse['horse_location'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Père</label>
                    <input type="text"
                           name="father"
                           value="<?= htmlentities($horse['horse_father'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Mère</label>
                    <input type="text"
                           name="mother"
                           value="<?= htmlentities($horse['horse_mother'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Numéro d'identification</label>
                    <input type="text"
                           name="id_number"
                           value="<?= htmlentities($horse['horse_id_number'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Numéro UELN</label>
                    <input type="text"
                           name="ueln"
                           value="<?= htmlentities($horse['horse_nb_ueln'] ?? '') ?>">
                </div>

                 <div class="af-field">
                    <label>Statut</label>

                    <select name="horse_status">

                        <option value="disponible"
                            <?= ($horse['horse_status'] ?? '') == 'disponible' ? 'selected' : '' ?>>
                            Disponible
                        </option>

                        <option value="indisponible"
                            <?= ($horse['horse_status'] ?? '') == 'indisponible' ? 'selected' : '' ?>>
                            Indisponible
                        </option>

                    </select>

                </div>

                 <div class="af-field">
                    <label>Prix de départ (€)</label>
                    <input type="number" name="price_starter" min="0">
                </div>

                 <div class="af-field af-field--full">
                    <label>Description</label>

                    <textarea name="description" rows="4"><?= htmlentities($horse['horse_description'] ?? '') ?></textarea>
                </div>

            </div>

            <button type="submit" class="btn btn-dark">
                Enregistrer
            </button>

        </form>

    </div>

</div>

<?php require_once '../footer.php'; ?>