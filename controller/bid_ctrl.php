<?php
session_start();
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$horseId = (int)($_POST['horse_id'] ?? 0);
$userId  = (int)$_SESSION['user_id'];

if ($horseId <= 0) {
    header("Location: ../views/horse_info.php?id=$horseId");
    exit;
}

try {

    // je sélectionne l'enchère du cheval donné avec ses informations principales
    $stmtAuction = $pdo->prepare("
        SELECT id_auction, auction_status, auction_end_date, auction_starting_price
        FROM auctions
        WHERE horse_id_fk = ?
        LIMIT 1
    ");
    $stmtAuction->execute([$horseId]);
    $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

    if (
        !$auction ||
        $auction['auction_status'] !== 'disponible' ||
        strtotime($auction['auction_end_date']) <= time()
    ) {
        header("Location: ../views/horse_info.php?id=$horseId");
        exit;
    }

    $auctionId = (int)$auction['id_auction'];

    // je récupère la meilleure enchère (montant le plus élevé) pour une enchère,
    // en triant les offres de la plus élevée à la plus faible
    $stmtLastBid = $pdo->prepare("
        SELECT id_bid, user_id_fk, bid_amount
        FROM bids
        WHERE auction_id_fk = ?
        ORDER BY bid_amount DESC
        LIMIT 1
    ");
    $stmtLastBid->execute([$auctionId]);
    $lastBid = $stmtLastBid->fetch(PDO::FETCH_ASSOC);

    if ($lastBid) {
        $currentPrice  = (float)$lastBid['bid_amount'];
        $previousUser  = (int)$lastBid['user_id_fk'];
        $previousBidId = (int)$lastBid['id_bid'];
    } else {
        $currentPrice  = (float)$auction['auction_starting_price'];
        $previousUser  = null;
        $previousBidId = null;
    }

    $increment = 50;
    $bidAmount = $currentPrice + $increment;

    if ($previousUser !== null && $previousUser === $userId) {
        header("Location: ../views/horse_info.php?id=$horseId");
        exit;
    }

    // j'insère une nouvelle enchère (utilisateur, montant, date actuelle, enchère liée)
    // NOW() ajoute automatiquement la date et l’heure actuelles
    $stmtInsert = $pdo->prepare("
        INSERT INTO bids (user_id_fk, bid_amount, bid_date, auction_id_fk)
        VALUES (?, ?, NOW(), ?)
    ");

    $stmtInsert->execute([
        $userId,
        $bidAmount,
        $auctionId
    ]);

    if ($previousUser && $previousUser !== $userId && $previousBidId) {

    // Je compte le nombre de fois où un utilisateur a été dépassé pour une enchère donnée
        $stmtCheck = $pdo->prepare("
            SELECT COUNT(*) 
            FROM outbid 
            WHERE bid_id_fk = ? AND user_id_fk = ?
        ");
        $stmtCheck->execute([$previousBidId, $previousUser]);

        if ($stmtCheck->fetchColumn() == 0) {

    // J'enregistre qu'un utilisateur a été dépassé sur une enchère (notification non vue)
    // bid_id_fk c'est l’enchère concernée
    // user_id_fk c'est l’utilisateur dépassé
    // seen = 0 c'est notification non vue
            $stmtOutbid = $pdo->prepare("
                INSERT INTO outbid (bid_id_fk, user_id_fk, seen)
                VALUES (?, ?, 0)
            ");
            $stmtOutbid->execute([
                $previousBidId,
                $previousUser
            ]);
        }
    }

    //  je marque comme vues toutes les notifications de dépassement pour un utilisateur
    // sur les enchères liées à une auction donnée
    $stmtClean = $pdo->prepare("
        UPDATE outbid
        SET seen = 1
        WHERE user_id_fk = ?
        AND bid_id_fk IN (SELECT id_bid FROM bids WHERE auction_id_fk = ?)
    ");
    $stmtClean->execute([$userId, $auctionId]);

    // et à jour le prix final d'une enchère avec le montant final enregistré
    $stmtUpdate = $pdo->prepare("
        UPDATE auctions
        SET auction_final_price = ?
        WHERE id_auction = ?
    ");
    $stmtUpdate->execute([
        $bidAmount,
        $auctionId
    ]);

    header("Location: ../views/horse_info.php?id=$horseId");
    exit;

} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}