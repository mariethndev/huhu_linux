<?php
require_once "../model/config.php";

$horses = [];

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE auction_status = ?
        ORDER BY auction_start_date DESC
    ");
    $stmt->execute(['disponible']);
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

        $currentPrice = $auction['auction_final_price'];

        if ($currentPrice === null) {
            $currentPrice = $auction['auction_starting_price'];
        }

        $horse['current_price'] = (float)$currentPrice;

        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];
        $horse['id_auction']         = $auction['id_auction'];

        $horses[] = $horse;
    }

} catch (PDOException $e) {
    echo $e->getMessage();
    $horses = [];
}

$count = count($horses);