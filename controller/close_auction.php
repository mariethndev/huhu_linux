<?php
session_start();
require_once "../model/config.php";

// je vérifie que l'utilisateur est organisateur
if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: ../views/homepage.php");
    exit;
}

// j'accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/organisateur_auctions.php");
    exit;
}

// je récupère l'id de l'enchère
$id = (int)($_POST['auction_id'] ?? 0);

// si id est valide je rediriger sur la page :
if ($id <= 0) {
    header("Location: ../views/organisateur_auctions.php");
    exit;
}

try {

    // je récupère l'enchère
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE id_auction = ?
    ");
    $stmt->execute([$id]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    // si pas trouvée → retour
    if (!$auction) {
        header("Location: ../views/organisateur_auctions.php");
        exit;
    }

    // je récupère la meilleure enchère (plus haute)
    $stmtBid = $pdo->prepare("
        SELECT user_id_fk, bid_amount
        FROM bids      WHERE horse_id_fk = ?
        ORDER BY bid_amount DESC
        LIMIT 1
    ");

    $stmtBid->execute([$auction['horse_id_fk']]);
    $bestBid = $stmtBid->fetch(PDO::FETCH_ASSOC);

    // je récupère le gagnant et le prix final
    $winnerId  = $bestBid['user_id_fk'] ?? null;
    $finalPrice = $bestBid['bid_amount'] ?? $auction['auction_starting_price'];

    // je met à jour l'enchère en "terminé"
    $stmtUpdate = $pdo->prepare("
        UPDATE auctions
        SET auction_status = 'terminé',
            auction_winner_id = ?,
            auction_final_price = ?
        WHERE id_auction = ?
    ");
    $stmtUpdate->execute([
        $winnerId,
        $finalPrice,
        $id
    ]);

    header("Location: ../views/organisateur_auctions.php");
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();
    header("Location: ../views/organisateur_auctions.php");
    exit;
}