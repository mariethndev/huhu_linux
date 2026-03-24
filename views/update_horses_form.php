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
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="af-alert af-alert--success">Cheval mis à jour</div>
    <?php endif; ?>

    <div class="af-card">

        <form action="../controller/update_horses_ctrl.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="horse_id" value="<?= (int)$horse['id_horse'] ?>">

            <div class="af-grid-2">

                <div class="af-field af-field--full">
                    <img src="<?= $imagePath ?>" style="max-width:200px">
                    <input type="file" name="horse_image">
                </div>

                <div class="af-field">
                    <label>Nom *</label>
                    <input type="text"
                           name="horse_name"
                           value="<?= htmlentities($horse['horse_name'] ?? '') ?>"
                           required>
                </div>

                <div class="af-field">
                    <label>Sexe</label>
                    <select name="horse_sex">
                        <option value="M" <?= ($horse['horse_sex'] ?? '')=='M'?'selected':'' ?>>Mâle</option>
                        <option value="F" <?= ($horse['horse_sex'] ?? '')=='F'?'selected':'' ?>>Femelle</option>
                    </select>
                </div>

                <div class="af-field">
                    <label>Date</label>
                    <input type="date" name="horse_birthdate" value="<?= $horse['horse_birthdate'] ?? '' ?>">
                </div>

                <div class="af-field">
                    <label>Race</label>
                    <input type="text" name="horse_breed" value="<?= htmlentities($horse['horse_breed'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Discipline</label>
                    <input type="text" name="horse_discipline" value="<?= htmlentities($horse['horse_discipline'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Robe</label>
                    <input type="text" name="horse_coat" value="<?= htmlentities($horse['horse_coat'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Taille</label>
                    <input type="number" name="horse_height" value="<?= $horse['horse_height'] ?? '' ?>">
                </div>

                <div class="af-field">
                    <label>Poids</label>
                    <input type="number" name="horse_weight" value="<?= $horse['horse_weight'] ?? '' ?>">
                </div>

                <div class="af-field af-field--full">
                    <label>Lieu</label>
                    <input type="text" name="horse_location" value="<?= htmlentities($horse['horse_location'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Père</label>
                    <input type="text" name="horse_father" value="<?= htmlentities($horse['horse_father'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Mère</label>
                    <input type="text" name="horse_mother" value="<?= htmlentities($horse['horse_mother'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>ID</label>
                    <input type="text" name="horse_id_number" value="<?= htmlentities($horse['horse_id_number'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>UELN</label>
                    <input type="text" name="horse_nb_ueln" value="<?= htmlentities($horse['horse_nb_ueln'] ?? '') ?>">
                </div>

                <div class="af-field">
                    <label>Statut</label>
                    <select name="horse_status">
                        <option value="disponible" <?= ($horse['horse_status'] ?? '')=='disponible'?'selected':'' ?>>Disponible</option>
                        <option value="indisponible" <?= ($horse['horse_status'] ?? '')=='indisponible'?'selected':'' ?>>Indisponible</option>
                    </select>
                </div>

                <!-- ✅ FIX PRIX -->
                <div class="af-field">
                    <label>Prix</label>
                    <input type="number" name="auction_starting_price">
                </div>

                <div class="af-field af-field--full">
                    <label>Description</label>
                    <textarea name="horse_description"><?= htmlentities($horse['horse_description'] ?? '') ?></textarea>
                </div>

            </div>

            <button type="submit">Enregistrer</button>

        </form>

    </div>
</div>

<?php require_once '../footer.php'; ?>