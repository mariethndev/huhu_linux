<?php
require_once "../model/config.php";

$auctions   = [];
$enCours    = [];
$terminees  = [];
$annulees   = [];

try {

    $stmt = $pdo->query("
        SELECT *
        FROM auctions
        ORDER BY auction_start_date DESC
    ");

    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($auctions as &$auction) {

        if (empty($auction['horse_id_fk'])) {
            continue;
        }

        $horseId = $auction['horse_id_fk'];

        // récupérer nom cheval
        $stmtHorse = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmtHorse->execute([$horseId]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        $auction['horse_name'] = $horse['horse_name'] ?? '—';

        // récupérer dernière enchère
        $stmtBid = $pdo->prepare("
            SELECT bid_amount, user_id_fk
            FROM bids
            WHERE horse_id_fk = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmtBid->execute([$horseId]);
        $lastBid = $stmtBid->fetch(PDO::FETCH_ASSOC);

        if ($lastBid) {
            $auction['last_bid']    = $lastBid['bid_amount'];
            $auction['last_bidder'] = $lastBid['user_id_fk'];
        } else {
            $auction['last_bid']    = $auction['auction_starting_price'];
            $auction['last_bidder'] = null;
        }

        // calcul statut simple
        if (!empty($auction['auction_end_date']) &&
            strtotime($auction['auction_end_date']) < time()) {

            $auction['auction_status'] = 'terminé';

        } elseif ($auction['auction_status'] === 'annulé') {

            $auction['auction_status'] = 'annulé';

        } else {

            $auction['auction_status'] = 'disponible';
        }
    }

    // tri par statut
    foreach ($auctions as $auction) {

        if ($auction['auction_status'] === "disponible") {
            $enCours[] = $auction;

        } elseif ($auction['auction_status'] === "terminé") {
            $terminees[] = $auction;

        } else {
            $annulees[] = $auction;
        }
    }

} catch (PDOException $e) {

    $auctions   = [];
    $enCours    = [];
    $terminees  = [];
    $annulees   = [];
}