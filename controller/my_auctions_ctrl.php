<?php
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

$userId = $_SESSION['user_id'];
$auctions = [];

try {

    // récupérer les chevaux sur lesquels l'utilisateur a enchéri
    $stmt = $pdo->prepare("
        SELECT DISTINCT horse_id_fk
        FROM bids
        WHERE user_id_fk = ?
    ");
    $stmt->execute([$userId]);
    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bids as $bid) {

        $horseId = $bid['horse_id_fk'];

        // récupérer cheval
        $stmtHorse = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmtHorse->execute([$horseId]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        if (!$horse) continue;

        // récupérer enchère
        $stmtAuction = $pdo->prepare("
            SELECT auction_status, auction_end_date, auction_starting_price
            FROM auctions
            WHERE horse_id_fk = ?
        ");
        $stmtAuction->execute([$horseId]);
        $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

        if (!$auction) continue;

        // prix actuel
        $stmtPrice = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmtPrice->execute([$horseId]);
        $lastBid = $stmtPrice->fetchColumn();

        $currentPrice =
            $lastBid ?: $auction['auction_starting_price'];

        // nombre participants
        $stmtCount = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmtCount->execute([$horseId]);
        $participants = (int)$stmtCount->fetchColumn();

        $auctions[] = [
            "id_horse" => $horseId,
            "horse_name" => $horse['horse_name'],
            "auction_status" => $auction['auction_status'],
            "auction_end_date" => $auction['auction_end_date'],
            "last_price" => $currentPrice,
            "participants" => $participants
        ];
    }

} catch (PDOException $e) {
    $auctions = [];
}