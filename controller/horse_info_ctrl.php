<?php
require_once "../model/config.php";

$horseId = (int)($_GET['id'] ?? 0);

if ($horseId <= 0) {
    header("Location: ../views/buy_a_horse.php");
    exit;
}

$horse = null;
$currentPrice = 0;
$nbVoters = 0;
$auctionStatus = 'indisponible';

try {

    // récupérer le cheval
    $stmt = $pdo->prepare("
        SELECT *
        FROM horses
        WHERE id_horse = ?
        AND horse_is_deleted = 0
    ");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horse) {
        header("Location: ../views/buy_a_horse.php");
        exit;
    }

    // récupérer l'enchère
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horseId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($auction) {
        $auctionStatus = $auction['auction_status'];
    }

    // récupérer le prix actuel
    $stmt = $pdo->prepare("
        SELECT MAX(bid_amount)
        FROM bids
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horseId]);
    $lastBid = $stmt->fetchColumn();

    $currentPrice =
        $lastBid ?: ($auction['auction_starting_price'] ?? 0);

    // nombre d'enchérisseurs
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT user_id_fk)
        FROM bids
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horseId]);
    $nbVoters = (int)$stmt->fetchColumn();

    $horse['voters'] = $nbVoters;

} catch (PDOException $e) {

    $horse = null;
}