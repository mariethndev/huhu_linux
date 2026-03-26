<?php
//  je  démarre la session
session_start();

//  je  charge la config bdd
require_once "../model/config.php";

//  je  vérifie que l'utilisateur est connecté ET organisateur
if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

//  je  accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/horses_list.php");
    exit;
}

//  je  récupère l'id du cheval
$horseId = (int)($_POST['horse_id'] ?? 0);

//  je  récupère les champs du formulaire
$name = trim($_POST['horse_name'] ?? '');

$sex        = $_POST['horse_sex'] ?? '';
$birthdate  = $_POST['horse_birthdate'] ?? '';
$breed      = $_POST['horse_breed'] ?? '';
$discipline = $_POST['horse_discipline'] ?? '';
$status     = $_POST['horse_status'] ?? '';

// champs optionnels (null si vide)
$height = !empty($_POST['horse_height']) ? $_POST['horse_height'] : null;
$weight = !empty($_POST['horse_weight']) ? $_POST['horse_weight'] : null;

$coat        = $_POST['horse_coat'] ?? '';
$location    = $_POST['horse_location'] ?? '';
$father      = $_POST['horse_father'] ?? '';
$mother      = $_POST['horse_mother'] ?? '';
$description = $_POST['horse_description'] ?? '';
$idNumber    = $_POST['horse_id_number'] ?? '';
$ueln        = $_POST['horse_nb_ueln'] ?? '';

// prix de départ de l'enchère
$price = (float)($_POST['auction_starting_price'] ?? 0);

// si id invalide ou nom vide → retour liste
if ($horseId <= 0 || $name === '') {
    header("Location: ../views/horses_list.php?status=danger");
    exit;
}

try {

    //  je  met à jour le cheval
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

    //  je  met à jour le prix de départ de l'enchère liée
    $stmt = $pdo->prepare("
        UPDATE auctions
        SET auction_starting_price=?
        WHERE horse_id_fk=?
    ");

    $stmt->execute([$price, $horseId]);

    // redirection avec succès
    header("Location: ../views/update_horses_form.php?id=$horseId&status=success");
    exit;

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback → retour liste
    header("Location: ../views/horses_list.php?status=danger");
    exit;
}