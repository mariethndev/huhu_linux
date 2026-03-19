<?php
session_start();
require_once "../model/config.php";

$data = json_decode(file_get_contents("php://input"), true);
$horseId = (int)($data['horse_id'] ?? 0);

if (!$horseId) {
    echo json_encode(["success" => false]);
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
    $bid = $stmt->fetch();

    if ($bid) {
        $price = (float)$bid['bid_amount'];
        $last = (int)$bid['user_id_fk'];
    } else {
        $price = (float)$pdo->query("
            SELECT auction_starting_price 
            FROM auctions 
            WHERE horse_id_fk = $horseId
        ")->fetchColumn();
        $last = null;
    }

    $user = $_SESSION['user_id'] ?? null;

    $hasBid = false;
    if ($user) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM bids 
            WHERE horse_id_fk = ? AND user_id_fk = ?
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