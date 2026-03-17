<?php
require_once "../model/config.php";

$auctions   = [];
$enCours    = [];
$terminees  = [];
$annulees   = [];

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        ORDER BY auction_start_date DESC
    ");

    $stmt->execute();
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($auctions as &$auction) {

        if (empty($auction['horse_id'])) {
            continue;
        }

        $horseId = $auction['horse_id'];

        $stmt = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmt->execute([$horseId]);
        $horse = $stmt->fetch(PDO::FETCH_ASSOC);

        $auction['horse_name'] = $horse['horse_name'] ?? '—';

        $stmt = $pdo->prepare("
            SELECT bid_amount, user_id
            FROM bids
            WHERE horse_id = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmt->execute([$horseId]);
        $lastBid = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastBid) {
            $auction['last_bid']    = $lastBid['bid_amount'];
            $auction['last_bidder'] = $lastBid['user_id'];
        } else {
            $auction['last_bid']    = 0;
            $auction['last_bidder'] = null;
        }

        $status = 'disponible';

        if (!empty($auction['auction_end_date'])) {
            if (strtotime($auction['auction_end_date']) < time()) {
                $status = 'terminé';
            }
        }

        $auction['auction_status'] = $status;

        $auction['auction_end_date'] = $auction['auction_end_date'] ?? null;
    }

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