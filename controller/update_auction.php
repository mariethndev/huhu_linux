<?php
// je démarre la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../model/config.php";

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $auctionId = (int)($_POST['auction_id'] ?? 0);
    $horseId   = (int)($_POST['horse_id'] ?? 0);

    $name        = $_POST['horse_name'] ?? '';
    $sex         = $_POST['horse_sex'] ?? '';
    $birthdate   = $_POST['horse_birthdate'] ?? '';
    $breed       = $_POST['horse_breed'] ?? '';
    $discipline  = $_POST['horse_discipline'] ?? '';
    $coat        = $_POST['horse_coat'] ?? '';
    $location    = $_POST['horse_location'] ?? '';
    $father      = $_POST['horse_father'] ?? '';
    $mother      = $_POST['horse_mother'] ?? '';
    $idNumber    = $_POST['horse_id_number'] ?? '';
    $ueln        = $_POST['horse_nb_ueln'] ?? '';
    $description = $_POST['horse_description'] ?? '';
    $statusHorse = $_POST['horse_status'] ?? 'disponible';

    $height = !empty($_POST['horse_height']) ? (int)$_POST['horse_height'] : null;
    $weight = !empty($_POST['horse_weight']) ? (int)$_POST['horse_weight'] : null;

    $price  = (float)($_POST['auction_starting_price'] ?? 0);
    $status = $_POST['auction_status'] ?? 'disponible';
    $end    = $_POST['auction_end_date'] ?? null;

    try {

        $stmt = $pdo->prepare("
            UPDATE horses
            SET horse_name=?, horse_sex=?, horse_birthdate=?,
                horse_breed=?, horse_discipline=?, horse_coat=?,
                horse_height=?, horse_weight=?, horse_location=?,
                horse_father=?, horse_mother=?, horse_id_number=?,
                horse_nb_ueln=?, horse_description=?, horse_status=?
            WHERE id_horse=?
        ");

        $stmt->execute([
            $name, $sex, $birthdate, $breed, $discipline,
            $coat, $height, $weight, $location,
            $father, $mother, $idNumber, $ueln,
            $description, $statusHorse, $horseId
        ]);

        $stmt = $pdo->prepare("
            UPDATE auctions
            SET auction_starting_price=?, auction_end_date=?, auction_status=?
            WHERE id_auction=?
        ");

        $stmt->execute([$price, $end, $status, $auctionId]);

        header("Location: ../views/update_auction.php?id=$auctionId&status=success");
        exit;

    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ../views/organisateur_auctions.php");
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE id_auction = ?");
    $stmt->execute([$id]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        header("Location: ../views/organisateur_auctions.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM horses WHERE id_horse = ?");
    $stmt->execute([$auction['horse_id_fk']]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horse) {
        header("Location: ../views/organisateur_auctions.php");
        exit;
    }

    $horse['horse_name']        = $horse['horse_name'] ?? '';
    $horse['horse_sex']         = $horse['horse_sex'] ?? 'M';
    $horse['horse_birthdate']   = $horse['horse_birthdate'] ?? '';
    $horse['horse_breed']       = $horse['horse_breed'] ?? '';
    $horse['horse_discipline']  = $horse['horse_discipline'] ?? '';
    $horse['horse_coat']        = $horse['horse_coat'] ?? '';
    $horse['horse_height']      = $horse['horse_height'] ?? '';
    $horse['horse_weight']      = $horse['horse_weight'] ?? '';
    $horse['horse_location']    = $horse['horse_location'] ?? '';
    $horse['horse_father']      = $horse['horse_father'] ?? '';
    $horse['horse_mother']      = $horse['horse_mother'] ?? '';
    $horse['horse_id_number']   = $horse['horse_id_number'] ?? '';
    $horse['horse_nb_ueln']     = $horse['horse_nb_ueln'] ?? '';
    $horse['horse_description'] = $horse['horse_description'] ?? '';
    $horse['horse_status']      = $horse['horse_status'] ?? 'disponible';

    // date format
    $dateValue = !empty($auction['auction_end_date'])
        ? date('Y-m-d\TH:i', strtotime($auction['auction_end_date']))
        : '';

    $auction['auction_status'] = $auction['auction_status'] ?? 'disponible';
    $auction['auction_starting_price'] = $auction['auction_starting_price'] ?? 0;

} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}