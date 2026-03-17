<?php
require_once "../model/config.php";

$horses = [];
$count  = 0;

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE auction_status = 'disponible'
        AND (auction_end_date IS NULL OR auction_end_date > NOW())
        ORDER BY auction_start_date DESC
        LIMIT 6
    ");
    $stmt->execute();

    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($auctions as $auction) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM horses
            WHERE id_horse = ?
            AND horse_is_deleted = 0
        ");
        $stmt->execute([$auction['horse_id']]);

        $horse = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$horse) {
            continue;
        }

        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id = ?
        ");
        $stmt->execute([$horse['id_horse']]);

        $lastBid = $stmt->fetchColumn();

        $horse['current_price'] = $lastBid ?: $auction['auction_starting_price'];
        $horse['auction_status']     = $auction['auction_status'];
        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];

        $horses[] = $horse;
    }

    $count = count($horses);

} catch (PDOException $e) {

    $horses = [];
    $count  = 0;
}