<?php
require_once "../model/config.php";

$auction_status = $_GET['auction_status'] ?? '';
$horses = [];

try {
    $stmt = $pdo->prepare("
        UPDATE auctions
        SET auction_status = 'terminé'
        WHERE auction_status = 'disponible'
        AND auction_end_date <= NOW()
    ");

    $stmt->execute();

    $stmt = $pdo->prepare("
        SELECT *
        FROM horses
        WHERE horse_is_deleted = 0
        ORDER BY horse_register_date DESC
    ");

    $stmt->execute();

    $allHorses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allHorses as $horse) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM auctions
            WHERE horse_id_fk = ?
        ");
    
        $stmt->execute([$horse['id_horse']]);

        $auction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$auction) {
            continue;
        }


        if ($auction_status != "" && $auction['auction_status'] != $auction_status) {
            continue;
        }

        $horse['auction_status'] = $auction['auction_status'];

        if ($auction['auction_status'] == "terminé") {

            $stmt = $pdo->prepare("
                SELECT MAX(bid_amount)
                FROM bid
                WHERE horse_id_fk = ?
            ");

            $stmt->execute([$horse['id_horse']]);

            $bestPrice = $stmt->fetchColumn();

            if ($bestPrice) {

                $stmt = $pdo->prepare("
                    SELECT user_id_fk
                    FROM bid
                    WHERE horse_id_fk = ?
                    AND bid_amount = ?
                ");

                $stmt->execute([
                    $horse['id_horse'],
                    $bestPrice
                ]);

                $winner = $stmt->fetch(PDO::FETCH_ASSOC);


                if ($winner) {

                    $stmt = $pdo->prepare("
                        SELECT user_name
                        FROM users
                        WHERE id_user = ?
                    ");

                    $stmt->execute([$winner['user_id_fk']]);
                    $name = $stmt->fetchColumn();
                    $horse['winner_name'] = $name ?: '—';

                } else {
                    $horse['winner_name'] = '—';
                }
            } else {
                $horse['winner_name'] = '—';
            }

        } else {
            $horse['winner_name'] = '—';
        }

        $horses[] = $horse;
    }

} catch (PDOException $e) {

    $horses = [];
}