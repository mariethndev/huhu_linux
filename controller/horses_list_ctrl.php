<?php
 
require_once "../model/config.php";
require_once "../views/horses_list.php";

$horses = [];
$statusFilter = $_GET['auction_status'] ?? '';

try {

     $stmtUpdate = $pdo->prepare("
        UPDATE auctions
        SET auction_status = 'terminé'
        WHERE auction_status = 'disponible'
        AND auction_end_date <= NOW()
    ");
    $stmtUpdate->execute();

     $stmt = $pdo->prepare("
        SELECT horses.*, auctions.auction_status, auctions.auction_end_date
        FROM horses
        LEFT JOIN auctions ON horses.id_horse = auctions.horse_id_fk
        WHERE horses.horse_is_deleted = 0
        ORDER BY horses.horse_register_date DESC
    ");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {

        $status = $row['auction_status'] ?? 'indisponible';

         if (!empty($statusFilter) && $status != $statusFilter) {
            continue;
        }

        $row['auction_status'] = $status;
        $row['winner_name'] = '—';

         if ($status === 'terminé') {

            $stmtBest = $pdo->prepare("
                SELECT user_id_fk
                FROM bids
                WHERE horse_id_fk = ?
                ORDER BY bid_amount DESC
                LIMIT 1
            ");
            $stmtBest->execute([$row['id_horse']]);

            $winnerId = $stmtBest->fetchColumn();

            if ($winnerId) {

                $stmtUser = $pdo->prepare("
                    SELECT user_name
                    FROM users
                    WHERE id_user = ?
                ");
                $stmtUser->execute([$winnerId]);

                $row['winner_name'] = $stmtUser->fetchColumn() ?: '—';
            }
        }

        $horses[] = $row;
    }

} catch (PDOException $e) {
    $horses = [];
}

