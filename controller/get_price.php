<?php
session_start();
require_once "../model/config.php";

// J'indique que le format est du JSON
header('Content-Type: application/json');

// je écupère uniquement depuis FormData (POST)
$horseId = isset($_POST['horse_id']) ? (int)$_POST['horse_id'] : 0;

//je vérifie si l’ID du cheval est valide et renvoie une réponse JSON
if ($horseId <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "invalid_id"
    ]);
    exit;
}

try {

    // je récupère la meilleure enchère actuelle
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
        // Si aucune enchère j'affiche le prix de départ
        $stmt = $pdo->prepare("
            SELECT auction_starting_price 
            FROM auctions 
            WHERE horse_id_fk = ?
        ");
        $stmt->execute([$horseId]);
        $price = (float)($stmt->fetchColumn() ?? 0);
        $last = null;
    }

    // Utilisateur connecté en session 
    $user = $_SESSION['user_id'] ?? null;

    $hasBid = false;

    if ($user) {
        // je vérifie si l'utilisateur a déjà enchéri
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM bids
            JOIN auctions ON bids.auction_id_fk = auctions.id_auction
            WHERE auctions.horse_id_fk = ? AND bids.user_id_fk = ?
        ");
        $stmt->execute([$horseId, $user]);
        $hasBid = $stmt->fetchColumn() > 0;
    }

// j'envoie une réponse JSON au JavaScript avec les informations de l'enchère
echo json_encode([
    "success" => true,           // Indique que la requête s'est bien passée
    "price" => $price,           // Prix actuel de l'enchère
    "last_bidder" => $last,      // ID du meilleur enchérisseur
    "current_user" => $user,     // ID de l'utilisateur connecté
    "has_bid" => $hasBid         // Indique si l'utilisateur a déjà enchéri
]);

} catch (Exception $e) {

    // En cas d'erreur serveur, on renvoie une réponse JSON avec une erreur
    echo json_encode([
        "success" => false,
        "error" => "server_error"
    ]);
}