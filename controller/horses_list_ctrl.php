<?php
require_once "../model/config.php";

$horses = [];
$statusFilter = $_GET['auction_status'] ?? '';

try {

    // mettre fin aux enchères expirées
    $pdo->query("
        UPDATE auctions
        SET auction_status = 'terminé'
        WHERE auction_status = 'disponible'
        AND auction_end_date <= NOW()
    ");

    // récupérer tous les chevaux
    $stmt = $pdo->query("
        SELECT *
        FROM horses
        WHERE horse_is_deleted = 0
        ORDER BY horse_register_date DESC
    ");

    $allHorses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allHorses as $horse) {

        // récupérer l'enchère
        $stmtAuction = $pdo->prepare("
            SELECT *
            FROM auctions
            WHERE horse_id_fk = ?
        ");
        $stmtAuction->execute([$horse['id_horse']]);
        $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

        if (!$auction) continue;

        // filtre statut
        if ($statusFilter && $auction['auction_status'] != $statusFilter) {
            continue;
        }

        $horse['auction_status'] = $auction['auction_status'];
        $horse['winner_name'] = '—';

        // si enchère terminée → trouver gagnant
        if ($auction['auction_status'] === 'terminé') {

            $stmtBest = $pdo->prepare("
                SELECT user_id_fk
                FROM bids
                WHERE horse_id_fk = ?
                ORDER BY bid_amount DESC
                LIMIT 1
            ");
            $stmtBest->execute([$horse['id_horse']]);
            $winnerId = $stmtBest->fetchColumn();

            if ($winnerId) {
                $stmtUser = $pdo->prepare("
                    SELECT user_name
                    FROM users
                    WHERE id_user = ?
                ");
                $stmtUser->execute([$winnerId]);
                $horse['winner_name'] = $stmtUser->fetchColumn() ?: '—';
            }
        }

        $horses[] = $horse;
    }

} catch (PDOException $e) {
    $horses = [];
}