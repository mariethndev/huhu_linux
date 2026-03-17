<?php
require_once "../model/config.php";

$horseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($horseId <= 0) {
    header("Location: /huhu/huhu/views/buy_a_horse.php");
    exit;
}

$horse = null;
$currentPrice = 0;
$nbVoters = 0;
$auctionStatus = 'indisponible';
$auction = null;
$timeRemaining = null;

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM horses
        WHERE id_horse = ?
        AND horse_is_deleted = 0
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horse) {
        header("Location: /huhu/huhu/views/buy_a_horse.php");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE horse_id = ?
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($auction) {
        $auctionStatus = $auction['auction_status'] ?? 'indisponible';
    }

    $stmt = $pdo->prepare("
        SELECT MAX(bid_amount)
        FROM bids
        WHERE horse_id = ?
    ");
    $stmt->execute([$horseId]);
    $maxBid = $stmt->fetchColumn();

    if ($maxBid) {
        $currentPrice = (float)$maxBid;
    } else {
        $currentPrice = (float)($auction['auction_starting_price'] ?? 0);
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT user_id)
        FROM bids
        WHERE horse_id = ?
    ");
    $stmt->execute([$horseId]);
    $nbVoters = (int)$stmt->fetchColumn();

    $horse['voters'] = $nbVoters;

} catch (PDOException $e) {
    echo $e->getMessage(); 
    exit;
}