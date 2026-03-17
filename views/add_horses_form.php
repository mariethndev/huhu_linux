<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: homepage.php");
    exit;
}

require_once '../head.php';
?>

<div class="af-page">

    <div class="af-page-header">
        <h1 class="af-page-title">Ajouter un cheval</h1>
        <p class="af-page-subtitle">
            Remplissez les informations pour créer un nouveau cheval.
        </p>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success') { ?>
        <div class="af-alert af-alert--success">
            Cheval ajouté avec succès.
        </div>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'danger') { ?>
        <div class="af-alert af-alert--danger">
            Veuillez remplir tous les champs obligatoires ou fichier invalide.
        </div>
    <?php } ?>

    <div class="af-card">

        <form action="../controller/add_horses_ctrl.php"
              method="post"
              enctype="multipart/form-data">

            <div class="af-grid-2">

                <div class="af-field af-field--full">

                    <label class="af-label">Photo du cheval</label>

                    <div style="margin-bottom:10px">
                        <img
                            id="horsePreview"
                            src="/huhu/huhu_linux/uploads/horses/horse_default.png"
                            style="max-width:250px;border-radius:8px;"
                        >
                    </div>

                    <input
                        type="file"
                        name="horse_image"
                        id="horseImageInput"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                </div>

                <div class="af-field">
                    <label>Nom *</label>
                    <input type="text" name="horse_name" required>
                </div>

                <div class="af-field">
                    <label>Sexe *</label>
                    <select name="horse_sex" required>
                        <option value="">Choisir</option>
                        <option value="M">Mâle</option>
                        <option value="F">Femelle</option>
                    </select>
                </div>

                <div class="af-field">
                    <label>Date de naissance *</label>
                    <input type="date" name="horse_birthdate" required>
                </div>

                <div class="af-field">
                    <label>Race *</label>
                    <input type="text" name="horse_breed" required>
                </div>

                <div class="af-field">
                    <label>Discipline</label>
                    <input type="text" name="horse_discipline">
                </div>

                <div class="af-field">
                    <label>Robe</label>
                    <input type="text" name="horse_coat">
                </div>

                <div class="af-field">
                    <label>Taille (cm)</label>
                    <input type="number" name="horse_height">
                </div>

                <div class="af-field">
                    <label>Poids (kg)</label>
                    <input type="number" name="horse_weight">
                </div>

                <div class="af-field af-field--full">
                    <label>Lieu</label>
                    <input type="text" name="horse_location">
                </div>

                <div class="af-field">
                    <label>Père</label>
                    <input type="text" name="horse_father">
                </div>

                <div class="af-field">
                    <label>Mère</label>
                    <input type="text" name="horse_mother">
                </div>

                <div class="af-field">
                    <label>Numéro d'identification</label>
                    <input type="text" name="horse_id_number">
                </div>

                <div class="af-field">
                    <label>Numéro UELN</label>
                    <input type="text" name="horse_nb_ueln">
                </div>

                <div class="af-field">
                    <label>Statut</label>
                    <select name="horse_status">
                        <option value="disponible">Disponible</option>
                        <option value="indisponible">Indisponible</option>
                    </select>
                </div>

                <div class="af-field">
                    <label>Prix de départ (€)</label>
                    <input type="number" name="auction_starting_price" min="0">
                </div>

                <div class="af-field af-field--full">
                    <label>Description</label>
                    <textarea name="horse_description" rows="4"></textarea>
                </div>

            </div>

            <button type="submit" class="btn btn-dark">
                Enregistrer
            </button>

        </form>

    </div>

</div>

<?php require_once '../footer.php'; ?>