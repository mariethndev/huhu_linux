<?php
require_once "../model/config.php";

$auctions = [];

try {

    $stmtUpdate = $pdo->prepare("
        UPDATE auctions
        SET auction_status = ?
        WHERE auction_status = ?
        AND auction_end_date <= NOW()
    ");
    $stmtUpdate->execute(['terminé', 'disponible']);

    $stmt = $pdo->prepare("
        SELECT 
            auctions.*,
            horses.horse_name
        FROM auctions
        LEFT JOIN horses ON horses.id_horse = auctions.horse_id_fk
        WHERE auctions.auction_status = ?
        ORDER BY auction_start_date DESC
    ");
    $stmt->execute(['disponible']);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $seen = [];

    foreach ($results as $auction) {

        $horseId = $auction['horse_id_fk'];

        if (!$horseId) continue;

        if (isset($seen[$horseId])) continue;

        $seen[$horseId] = true;

        $stmtBid = $pdo->prepare("
            SELECT bid_amount, user_id_fk
            FROM bids
            WHERE auction_id_fk = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmtBid->execute([$auction['id_auction']]);
        $lastBid = $stmtBid->fetch(PDO::FETCH_ASSOC);

        if ($lastBid) {
            $auction['last_bid'] = $lastBid['bid_amount'];
            $winnerId = $lastBid['user_id_fk'];
        } else {
            $auction['last_bid'] = $auction['auction_starting_price'];
            $winnerId = null;
        }

        if ($winnerId) {

            $stmtUser = $pdo->prepare("
                SELECT user_name
                FROM users
                WHERE id_user = ?
            ");
            $stmtUser->execute([$winnerId]);

            $auction['last_bidder_name'] = $stmtUser->fetchColumn() ?: '—';

        } else {
            $auction['last_bidder_name'] = '—';
        }

        $auctions[] = $auction;
    }

} catch (PDOException $e) {
    echo $e->getMessage();
    $auctions = [];
}