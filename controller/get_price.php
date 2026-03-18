<?php
session_start();
require_once "../model/config.php";

$input = json_decode(file_get_contents("php://input"), true);

$horseId = (int)($input['horse_id'] ?? 0);

if ($horseId <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "ID invalide"
    ]);
    exit;
}

try {

     $stmt = $pdo->prepare("
        SELECT bid_amount, user_id_fk
        FROM bids
        WHERE horse_id_fk = ?
        ORDER BY bid_amount DESC
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $lastBid = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastBid) {
        $price = (float)$lastBid['bid_amount'];
        $lastBidder = (int)$lastBid['user_id_fk'];
    } else {
         $stmtAuction = $pdo->prepare("
            SELECT auction_starting_price
            FROM auctions
            WHERE horse_id_fk = ?
            LIMIT 1
        ");
        $stmtAuction->execute([$horseId]);

        $price = (float)($stmtAuction->fetchColumn() ?? 0);
        $lastBidder = null;
    }

     $currentUser = $_SESSION['user_id'] ?? null;
    $hasBid = false;

    if ($currentUser) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM bids
            WHERE horse_id_fk = ? AND user_id_fk = ?
        ");
        $stmt->execute([$horseId, $currentUser]);
        $hasBid = $stmt->fetchColumn() > 0;
    }

    echo json_encode([
        "success" => true,
        "price" => $price,
        "last_bidder" => $lastBidder,
        "current_user" => $currentUser,
        "has_bid" => $hasBid,
        "DEBUG_user" => $_SESSION['user_id'] ?? null
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}