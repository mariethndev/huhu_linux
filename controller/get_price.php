<?php
session_start();
require_once "../model/config.php";

header('Content-Type: application/json');

// Récupère uniquement depuis FormData (POST)
$horseId = isset($_POST['horse_id']) ? (int)$_POST['horse_id'] : 0;

if ($horseId <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "invalid_id"
    ]);
    exit;
}

try {

    // Récupère la meilleure enchère actuelle
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
        // Si aucune enchère → prix de départ
        $stmt = $pdo->prepare("
            SELECT auction_starting_price 
            FROM auctions 
            WHERE horse_id_fk = ?
        ");
        $stmt->execute([$horseId]);
        $price = (float)($stmt->fetchColumn() ?? 0);
        $last = null;
    }

    // Utilisateur connecté
    $user = $_SESSION['user_id'] ?? null;

    $hasBid = false;

    if ($user) {
        // Vérifie si l'utilisateur a déjà enchéri
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

    echo json_encode([
        "success" => false,
        "error" => "server_error"
    ]);
}