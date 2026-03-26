<?php
//  je  démarre la session
session_start();

//  je  charge la config bdd
require_once "../model/config.php";

// si pas connecté → retour login
if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

//  je  récupère l'id user
$userId = $_SESSION['user_id'];

//  je  prépare les groupes d'enchères
$groupedAuctions = [
    "en_cours" => [],
    "annulees" => [],
    "terminees" => [],
    "remportees" => []
];

try {

    //  je  récupère les enchères où l'utilisateur s'est fait dépasser (non vues)
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

    // nombre d'alertes "dépassé"
    $outbidCount = count($outbids);

    //  je  récupère toutes les enchères où le user a participé
    $stmt = $pdo->prepare("
        SELECT DISTINCT auctions.horse_id_fk, auctions.id_auction
        FROM bids
        JOIN auctions ON bids.auction_id_fk = auctions.id_auction
        WHERE bids.user_id_fk = ?
    ");
    $stmt->execute([$userId]);
    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //  je  boucle sur chaque enchère
    foreach ($bids as $bid) {

        $horseId = (int)$bid['horse_id_fk'];
        $auctionId = (int)$bid['id_auction'];

        //  je  récupère le nom du cheval
        $stmtHorse = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmtHorse->execute([$horseId]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        // si pas trouvé → skip
        if (!$horse) continue;

        //  je  récupère les infos de l'enchère
        $stmtAuction = $pdo->prepare("
            SELECT id_auction, auction_status, auction_end_date, auction_starting_price
            FROM auctions
            WHERE id_auction = ?
        ");
        $stmtAuction->execute([$auctionId]);
        $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

        // si pas trouvé → skip
        if (!$auction) continue;

        //  je  récupère le prix actuel (plus grosse enchère)
        $stmtPrice = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmtPrice->execute([$auctionId]);
        $lastBid = $stmtPrice->fetchColumn();

        // fallback prix de départ
        $currentPrice = $lastBid ?: $auction['auction_starting_price'];

        //  je  récupère la dernière enchère du user
        $stmtMyBid = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE auction_id_fk = ? AND user_id_fk = ?
        ");
        $stmtMyBid->execute([$auctionId, $userId]);
        $myLastBid = $stmtMyBid->fetchColumn();

        //  je  récupère l'id de sa meilleure enchère
        $stmtMyBidId = $pdo->prepare("
            SELECT id_bid
            FROM bids
            WHERE auction_id_fk = ? AND user_id_fk = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmtMyBidId->execute([$auctionId, $userId]);
        $myBidId = $stmtMyBidId->fetchColumn();

        //  je  check si l'utilisateur s'est fait dépasser
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

        // nombre de participants
        $stmtCount = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmtCount->execute([$auctionId]);
        $participants = (int)$stmtCount->fetchColumn();

        //  je  récupère le gagnant (plus grosse enchère)
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
        $winnerId   = $winnerData['id_user'] ?? null;

        //  je  prépare les données à afficher
        $data = [
            "id_horse" => $horseId,
            "horse_name" => $horse['horse_name'],
            "auction_end_date" => $auction['auction_end_date'],
            "last_price" => $currentPrice,
            "my_last_bid" => $myLastBid,
            "participants" => $participants,
            "is_outbid" => $isOutbid,
            "last_bidder" => $lastBidder,
        ];

        //  je  normalise le statut
        $status = strtolower(trim($auction['auction_status']));

        //  je  range dans le bon groupe
        if ($status === 'disponible') {
            $groupedAuctions["en_cours"][] = $data;

        } elseif ($status === 'annulé' || $status === 'annule') {
            $groupedAuctions["annulees"][] = $data;

        } elseif ($status === 'terminé' || $status === 'termine') {

            // si c'est lui le gagnant
            if ($winnerId == $userId) {
                $groupedAuctions["remportees"][] = $data;
            } else {
                $groupedAuctions["terminees"][] = $data;
            }
        }
    }

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback propre
    $groupedAuctions = [
        "en_cours" => [],
        "annulees" => [],
        "terminees" => [],
        "remportees" => []
    ];

    $outbids = [];
    $outbidCount = 0;
}