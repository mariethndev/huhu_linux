<?php
session_start();
require_once "../model/config.php";

$horseId = (int)($_GET['id'] ?? 0);

if ($horseId <= 0) {
    header("Location: ../views/buy_a_horse.php");
    exit;
}

$horse = null;
$auction = [];
$userLogged = !empty($_SESSION['user_id']);

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM horses
        WHERE id_horse = ?
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horse) {
        header("Location: ../views/buy_a_horse.php");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE horse_id_fk = ?
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $auctionData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $auctionId = $auctionData['id_auction'] ?? 0;

    $lastBid = null;

    if ($auctionId) {
        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmt->execute([$auctionId]);
        $lastBid = $stmt->fetchColumn();
    }

    if ($lastBid !== null && $lastBid > 0) {
        $currentPrice = (float)$lastBid;
    } elseif (!empty($auctionData['auction_starting_price'])) {
        $currentPrice = (float)$auctionData['auction_starting_price'];
    } else {
        $currentPrice = 0;
    }

    $participants = 0;

    if ($auctionId) {
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmt->execute([$auctionId]);
        $participants = (int)$stmt->fetchColumn();
    }

    $status = strtolower(trim($auctionData['auction_status'] ?? ''));
    $isEnded = !empty($auctionData['auction_end_date']) &&
               strtotime($auctionData['auction_end_date']) <= time();

    $isActive = ($status === 'disponible' && !$isEnded);

    $auction = [
        "id_auction"    => $auctionId,
        "is_active"     => $isActive,
        "status_label"  => $isActive ? "En cours" : "Clôturée",
        "badge_class"   => $isActive ? "bg-success" : "bg-danger",
        "current_price" => $currentPrice,
        "participants"  => $participants,
    ];

} catch (PDOException $e) {

    error_log($e->getMessage());

    $horse = null;
    $auction = [
        "id_auction" => 0,
        "is_active" => false,
        "status_label" => "Erreur",
        "badge_class" => "bg-danger",
        "current_price" => 0,
        "participants" => 0,
    ];
}