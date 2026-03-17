<?php
session_start();
require_once '../controller/horse_info_ctrl.php';
require_once '../head.php';

$defaultImage = "/huhu/huhu_linux/uploads/horses/horse_default.png";

$imagePath = !empty($horse['horse_image'])
    ? "/huhu/huhu_linux/uploads/horses/" . htmlentities($horse['horse_image'])
    : $defaultImage;

$birthdateFormatted = '—';
if (!empty($horse['horse_birthdate'])) {
    $birthdateFormatted = date('d/m/Y', strtotime($horse['horse_birthdate']));
}
?>

<div class="container-info-horse">

    <div class="left-card-horse">

        <h1 class="horse-title">
            Fiche de <?= htmlentities($horse['horse_name'] ?? 'Cheval') ?>
        </h1>

        <div class="image-horse-info">
            <img src="<?= $imagePath ?>" alt="Photo du cheval">
        </div>

        <div class="horse-details-container">
            <a href="/huhu/huhu_linux/views/buy_a_horse.php" class="back-horse-info">
                RETOUR
            </a>
        </div>

    </div>


    <div class="right-card-horse">

        <div class="infos">
            <div class="card-info">
                <h3>DÉTAILS</h3>

                <p>
                    Numéro :
                    <?= htmlentities($horse['horse_id_number'] ?? '—') ?>
                </p>

                <p>
                    Naissance :
                    <?= $birthdateFormatted ?>
                </p>

                <p>
                    Discipline :
                    <strong>
                        <?= htmlentities($horse['horse_discipline'] ?? '—') ?>
                    </strong>
                </p>

                <p>
                    Lieu :
                    <?= htmlentities($horse['horse_location'] ?? '—') ?>
                </p>

                <p>
                    Date d'enregistrement :
                    <?= !empty($horse['horse_register_date'])
                        ? date('d/m/Y', strtotime($horse['horse_register_date']))
                        : '—' ?>
                </p>
            </div>

            <div class="card-info">
                <h3>MORPHOLOGIE</h3>

                <p>
                    Robe :
                    <?= htmlentities($horse['horse_coat'] ?? '—') ?>
                </p>

                <p>
                    Taille :
                    <?= !empty($horse['horse_height'])
                        ? htmlentities($horse['horse_height']) . ' cm'
                        : 'NC' ?>
                </p>

                <p>
                    Poids :
                    <?= !empty($horse['horse_weight'])
                        ? htmlentities($horse['horse_weight']) . ' kg'
                        : 'NC' ?>
                </p>

                <p>
                    Sexe :
                    <?= htmlentities($horse['horse_sex'] ?? '—') ?>
                </p>
            </div>

            <div class="card-info">
                <h3>RACE</h3>

                <p>
                    <?= htmlentities($horse['horse_breed'] ?? '—') ?>
                </p>

                <p>
                    Numéro UELN :
                    <?= htmlentities($horse['horse_nb_ueln'] ?? '—') ?>
                </p>
            </div>

            <div class="card-info">
                <h3>PARENTS</h3>

                <p>
                    Mère :
                    <?= htmlentities($horse['horse_mother'] ?? '—') ?>
                </p>

                <p>
                    Père :
                    <?= htmlentities($horse['horse_father'] ?? '—') ?>
                </p>
            </div>

            <div class="card-info">
                <h3>DESCRIPTION</h3>
                <p>
                    <?php
                    $description = trim($horse['horse_description'] ?? '');

                    echo ($description === '' || $description === '...')
                        ? 'Aucune description disponible.'
                        : nl2br(htmlentities($description));
                    ?>
                </p>
            </div>


            <div class="card-info">
                <h3>ENCHÈRE</h3>

                <p>
                    Statut :
                    <?php if (($auctionStatus ?? '') === 'disponible'): ?>
                        <span class="badge bg-success">
                            Disponible
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger">
                            Clôturée
                        </span>
                    <?php endif; ?>
                </p>

                <p>
                    Prix actuel :
                    <strong>
                        <?= number_format((float)($currentPrice ?? 0), 0, ',', ' ') ?> €
                    </strong>
                </p>

 

                <p class="voters-info btn btn-secondary p-2">
                    <?= $horse['voters'] ?? 0 ?>
                    participant(s)
                </p>

            </div>
        </div>

        <div class="cta-horse-info">

            <?php if (!isset($_SESSION['user_id'])): ?>

                <p>Inscription requise pour participer.</p>

                <a
                    href="/huhu/huhu_linux/views/register_form.php"
                    class="btn-horse-info"
                >
                    S'inscrire
                </a>

            <?php else: ?>

                <button
                    class="btn-bid"
                    data-horse-name="<?= htmlentities($horse['horse_name']) ?>"
                    data-price="<?= (float)($currentPrice ?? 0) ?>"
                    data-horse-id="<?= $horse['id_horse'] ?>"
                    data-image="<?= $imagePath ?>"
                >
                    Faire une offre
                </button>

            <?php endif; ?>
        </div>
    </div>
</div>

<div id="bidModal" class="custom-modal hidden">

    <div class="modal-card">
        <div class="modal-image">
            <img src="" alt="">
            <span class="modal-close">
                &times;
            </span>
        </div>


        <div class="modal-body">

            <h2 id="modalHorseName"></h2>

            <p>
                Prix actuel :
                <strong id="modalCurrentPrice"></strong>
            </p>


            <form
                action="/huhu/huhu_linux/controller/bid_ctrl.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="horse_id"
                    id="modalHorseId"
                >


                <div class="bid-input">
                    <button type="button" id="bidMinus">
                        -
                    </button>

                    <input
                        type="number"
                        name="bid_amount"
                        id="bidAmount"
                        required
                    >

                    <button type="button" id="bidPlus">
                        +
                    </button>
                </div>

                <button
                    type="submit"
                    class="btn-bid-now"
                >
                    Enchérir
                </button>

            </form>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>