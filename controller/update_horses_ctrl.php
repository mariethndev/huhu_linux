<?php
session_start();
require_once "../model/config.php";

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/horses_list.php");
    exit;
}

$horseId = (int)($_POST['horse_id'] ?? 0);

$name = trim($_POST['horse_name'] ?? '');

$sex        = $_POST['horse_sex'] ?? '';
$birthdate  = $_POST['horse_birthdate'] ?? '';
$breed      = $_POST['horse_breed'] ?? '';
$discipline = $_POST['horse_discipline'] ?? '';
$status     = $_POST['horse_status'] ?? '';

$height = !empty($_POST['horse_height']) ? $_POST['horse_height'] : null;
$weight = !empty($_POST['horse_weight']) ? $_POST['horse_weight'] : null;

$coat        = $_POST['horse_coat'] ?? '';
$location    = $_POST['horse_location'] ?? '';
$father      = $_POST['horse_father'] ?? '';
$mother      = $_POST['horse_mother'] ?? '';
$description = $_POST['horse_description'] ?? '';
$idNumber    = $_POST['horse_id_number'] ?? '';
$ueln        = $_POST['horse_nb_ueln'] ?? '';

$price = (float)($_POST['auction_starting_price'] ?? 0);

if ($horseId <= 0 || $name === '') {
    header("Location: ../views/horses_list.php?status=danger");
    exit;
}

try {

    $stmt = $pdo->prepare("
        UPDATE horses
        SET horse_name=?, horse_sex=?, horse_birthdate=?,
            horse_breed=?, horse_discipline=?, horse_status=?,
            horse_coat=?, horse_height=?, horse_weight=?,
            horse_location=?, horse_father=?, horse_mother=?,
            horse_description=?, horse_id_number=?, horse_nb_ueln=?
        WHERE id_horse=?
    ");

    $stmt->execute([
        $name,
        $sex,
        $birthdate,
        $breed,
        $discipline,
        $status,
        $coat,
        $height,
        $weight,
        $location,
        $father,
        $mother,
        $description,
        $idNumber,
        $ueln,
        $horseId
    ]);

    $stmt = $pdo->prepare("
        UPDATE auctions
        SET auction_starting_price=?
        WHERE horse_id_fk=?
    ");

    $stmt->execute([$price, $horseId]);

    header("Location: ../views/update_horses_form.php?id=$horseId&status=success");
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();
    header("Location: ../views/horses_list.php?status=danger");
    exit;
}