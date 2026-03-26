<?php
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

// je récupère l'id de l'utilisateur connecté
$userId = $_SESSION['user_id'];

// je prépare un message pour l'utilisateur (en cas d'erreur)
$message = "";

// je crée un tableau pour classer les enchères par statut
$groupedAuctions = [
    "en_cours" => [],
    "annulees" => [],
    "terminees" => [],
    "remportees" => []
];

try {

    // je récupère les enchères où j'ai été dépassé et que je n’ai pas encore vues
    // la table outbid permet de stocker les notifications de dépassement (en tête, surenchir)
    $stmtOutbidList = $pdo->prepare("
        SELECT DISTINCT auctions.horse_id_fk, horses.horse_name
        FROM outbid
        JOIN bids ON outbid.bid_id_fk = bids.id_bid
        JOIN auctions ON bids.auction_id_fk = auctions.id_auction
        JOIN horses ON auctions.horse_id_fk = horses.id_horse
        WHERE outbid.user_id_fk = ? AND outbid.seen = 0
    ");
    $stmtOutbidList->execute([$userId]);
    $outbids = $stmtOutbidList->fetchAll(PDO::FETCH_ASSOC);

    // PARTIE ALERTE
    $outbidCount = count($outbids);

    // je récupère toutes les enchères auxquelles j’ai participé
    $stmt = $pdo->prepare("
        SELECT DISTINCT auctions.horse_id_fk, auctions.id_auction
        FROM bids
        JOIN auctions ON bids.auction_id_fk = auctions.id_auction
        WHERE bids.user_id_fk = ?
    ");
    $stmt->execute([$userId]);
    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // PARTIE ENCHERE
    foreach ($bids as $bid) {

        $horseId = (int)$bid['horse_id_fk'];
        $auctionId = (int)$bid['id_auction'];

        // je récupère le cheval
        $stmtHorse = $pdo->prepare("SELECT horse_name, horse_image FROM horses WHERE id_horse = ?");
        $stmtHorse->execute([$horseId]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        // je récupère l'enchère
        $stmtAuction = $pdo->prepare("
            SELECT id_auction, auction_status, auction_end_date, auction_starting_price
            FROM auctions WHERE id_auction = ?
        ");
        $stmtAuction->execute([$auctionId]);
        $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

        if ($horse && $auction) {

            // PARTIE PRIX
            $stmtPrice = $pdo->prepare("SELECT MAX(bid_amount) FROM bids WHERE auction_id_fk = ?");
            $stmtPrice->execute([$auctionId]);
            $lastBid = $stmtPrice->fetchColumn();

            // je détermine le prix actuel avec if / elseif / else
            if ($lastBid > 0) {
                // il y a une enchère
                $currentPrice = $lastBid;

            } elseif (!empty($auction['auction_starting_price'])) {
                // pas d'enchère → prix de départ
                $currentPrice = $auction['auction_starting_price'];

            } else {
                // sécurité (cas improbable)
                $currentPrice = 0;
            }

            // je récupère MA dernière enchère
            $stmtMyBid = $pdo->prepare("
                SELECT MAX(bid_amount)
                FROM bids
                WHERE auction_id_fk = ? AND user_id_fk = ?
            ");
            $stmtMyBid->execute([$auctionId, $userId]);
            $myLastBid = $stmtMyBid->fetchColumn();

            // je récupère l'id de mon meilleur bid
            $stmtMyBidId = $pdo->prepare("
                SELECT id_bid
                FROM bids
                WHERE auction_id_fk = ? AND user_id_fk = ?
                ORDER BY bid_amount DESC
                LIMIT 1
            ");
            $stmtMyBidId->execute([$auctionId, $userId]);
            $myBidId = $stmtMyBidId->fetchColumn();

            // je vérifie si j’ai été dépassé
            $isOutbid = false;

            if ($myBidId) {
                $stmtOutbidCheck = $pdo->prepare("
                    SELECT COUNT(*) 
                    FROM outbid 
                    WHERE user_id_fk = ? 
                    AND bid_id_fk = ?
                    AND seen = 0
                ");
                $stmtOutbidCheck->execute([$userId, $myBidId]);
                $isOutbid = $stmtOutbidCheck->fetchColumn() > 0;
            }

            // je compte les participants
            $stmtCount = $pdo->prepare("
                SELECT COUNT(DISTINCT user_id_fk)
                FROM bids
                WHERE auction_id_fk = ?
            ");
            $stmtCount->execute([$auctionId]);
            $participants = (int)$stmtCount->fetchColumn();

            // je récupère le gagnant
            $stmtWinner = $pdo->prepare("
                SELECT users.user_name, users.id_user
                FROM bids
                JOIN users ON bids.user_id_fk = users.id_user
                WHERE bids.auction_id_fk = ?
                ORDER BY bids.bid_amount DESC
                LIMIT 1
            ");
            $stmtWinner->execute([$auctionId]);
            $winnerData = $stmtWinner->fetch(PDO::FETCH_ASSOC);

            $lastBidder = $winnerData['user_name'] ?? 'Aucun';
            $winnerId = $winnerData['id_user'] ?? 0;

            // je prépare les données
            $data = [
                "id_horse" => $horseId,
                "horse_name" => $horse['horse_name'],
                "horse_image" => $horse['horse_image'] ?? 'horse_default.png',
                "auction_end_date" => $auction['auction_end_date'],
                "last_price" => $currentPrice,
                "my_last_bid" => $myLastBid,
                "participants" => $participants,
                "is_outbid" => $isOutbid,
                "last_bidder" => $lastBidder,
            ];

            // STATUT
            $status = strtolower(trim($auction['auction_status']));

            if ($status === 'disponible') {
                $groupedAuctions["en_cours"][] = $data;

            } elseif ($status === 'annulé' || $status === 'annule') {
                $groupedAuctions["annulees"][] = $data;

            } elseif ($status === 'terminé' || $status === 'termine') {

                if ($winnerId == $userId) {
                    $groupedAuctions["remportees"][] = $data;
                } else {
                    $groupedAuctions["terminees"][] = $data;
                }
            }

        } else {

            if (empty($message)) {
                $message = "Certaines enchères n'ont pas pu être chargées.";
            }
        }
    }

} catch (PDOException $e) {
    $message = "Une erreur est survenue.";
}