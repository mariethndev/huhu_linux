<?php
session_start();
require_once "../model/config.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$horseId = 0;

if ($data && isset($data['horse_id'])) {
    $horseId = (int)$data['horse_id'];
} elseif (isset($_GET['horse_id'])) {
    $horseId = (int)$_GET['horse_id'];
}

if ($horseId <= 0) {
    echo json_encode(["success" => false]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT bids.bid_amount, bids.user_id_fk
        FROM bids
        JOIN auctions ON bids.auction_id_fk = auctions.id_auction
        WHERE auctions.horse_id_fk = ?
        ORDER BY bids.bid_amount DESC
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $bid = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bid) {
        $price = (float)$bid['bid_amount'];
        $last = (int)$bid['user_id_fk'];
    } else {
        $stmt = $pdo->prepare("
            SELECT auction_starting_price 
            FROM auctions 
            WHERE horse_id_fk = ?
        ");
        $stmt->execute([$horseId]);
        $price = (float)($stmt->fetchColumn() ?? 0);
        $last = null;
    }

    $user = $_SESSION['user_id'] ?? null;

    $hasBid = false;
    if ($user) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM bids
            JOIN auctions ON bids.auction_id_fk = auctions.id_auction
            WHERE auctions.horse_id_fk = ? AND bids.user_id_fk = ?
        ");
        $stmt->execute([$horseId, $user]);
        $hasBid = $stmt->fetchColumn() > 0;
    }

    echo json_encode([
        "success" => true,
        "price" => $price,
        "last_bidder" => $last,
        "current_user" => $user,
        "has_bid" => $hasBid
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false]);
}