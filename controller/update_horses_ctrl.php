<?php
session_start();
require_once "../model/config.php";

if (
    empty($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/horses_list.php");
    exit;
}

$horseId = (int)($_POST['horse_id'] ?? 0);

$name        = trim($_POST['name'] ?? '');
$sex         = $_POST['sex'] ?? '';
$birthdate   = $_POST['birthdate'] ?? '';
$breed       = trim($_POST['race'] ?? '');
$discipline  = trim($_POST['discipline'] ?? '');
$status      = $_POST['horse_status'] ?? '';

$height      = $_POST['height'] ?: null;
$weight      = $_POST['weight'] ?: null;

$coat        = trim($_POST['coat'] ?? '');
$location    = trim($_POST['location'] ?? '');
$father      = trim($_POST['father'] ?? '');
$mother      = trim($_POST['mother'] ?? '');
$description = trim($_POST['description'] ?? '');
$idNumber    = trim($_POST['id_number'] ?? '');
$ueln        = trim($_POST['ueln'] ?? '');

$price = (float)($_POST['price_starter'] ?? 0);

$imageName = null;

if (!empty($_FILES['horse_image']['name']) && $_FILES['horse_image']['error'] === 0) {

    $imageName = time() . "_" . basename($_FILES['horse_image']['name']);

    move_uploaded_file(
        $_FILES['horse_image']['tmp_name'],
        "../uploads/horses/" . $imageName
    );
}

try {

    // UPDATE cheval
    if ($imageName) {

        $stmt = $pdo->prepare("
            UPDATE horses
            SET horse_name=?, horse_sex=?, horse_birthdate=?,
                horse_breed=?, horse_discipline=?, horse_status=?,
                horse_coat=?, horse_height=?, horse_weight=?,
                horse_location=?, horse_father=?, horse_mother=?,
                horse_description=?, horse_id_number=?, horse_nb_ueln=?,
                horse_image=?
            WHERE id_horse=?
        ");

        $stmt->execute([
            $name, $sex, $birthdate,
            $breed, $discipline, $status,
            $coat, $height, $weight,
            $location, $father, $mother,
            $description, $idNumber, $ueln,
            $imageName, $horseId
        ]);

    } else {

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
            $name, $sex, $birthdate,
            $breed, $discipline, $status,
            $coat, $height, $weight,
            $location, $father, $mother,
            $description, $idNumber, $ueln,
            $horseId
        ]);
    }

    // UPDATE enchère
    $stmt = $pdo->prepare("
        UPDATE auctions
        SET auction_starting_price=?,
            auction_status=?
        WHERE horse_id_fk=?
    ");

    $stmt->execute([
        $price,
        $status,
        $horseId
    ]);

    header("Location: ../views/update_horses_form.php?id=$horseId&status=success");
    exit;

} catch (PDOException $e) {

    header("Location: ../views/horses_list.php?status=danger");
    exit;
}