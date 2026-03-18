<?php
require_once "../model/config.php";

$horses = [];

try {

     $stmt = $pdo->query("
        SELECT *
        FROM auctions
        WHERE auction_status = 'disponible'        
        ORDER BY auction_start_date DESC
            ");

    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($auctions as $auction) {

         $stmtHorse = $pdo->prepare("
            SELECT *
            FROM horses
            WHERE id_horse = ?
            AND horse_is_deleted = 0
        ");
        $stmtHorse->execute([$auction['horse_id_fk']]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        if (!$horse) continue;

         $stmtPrice = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmtPrice->execute([$horse['id_horse']]);
        $lastBid = $stmtPrice->fetchColumn();

        $horse['current_price'] =
        $lastBid ? $lastBid : $auction['auction_starting_price'];

        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];

        $horses[] = $horse;
    }

} catch (PDOException $e) {

    $horses = [];
}

$count = count($horses);