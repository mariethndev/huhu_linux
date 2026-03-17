<?php 

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

require_once "../model/config.php";

$userId = $_SESSION['user_id'];
$auctions = [];

try {

    $stmt = $pdo->prepare("
        SELECT DISTINCT horse_id
        FROM bids
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);

    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bids as $bid) {

        $horseId = $bid['horse_id'];

        $stmt = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmt->execute([$horseId]);
        $horse = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$horse) {
            continue;
        }

        $stmt = $pdo->prepare("
            SELECT auction_status, auction_end_date, auction_starting_price
            FROM auctions
            WHERE horse_id = ?
        ");
        $stmt->execute([$horseId]);
        $auction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$auction) {
            continue;
        }

        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id = ?
        ");
        $stmt->execute([$horseId]);

        $lastBid = $stmt->fetchColumn();

        $currentPrice = $lastBid
            ? (float)$lastBid
            : (float)$auction['auction_starting_price'];

        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id)
            FROM bids
            WHERE horse_id = ?
        ");
        $stmt->execute([$horseId]);

        $participants = (int)$stmt->fetchColumn();

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