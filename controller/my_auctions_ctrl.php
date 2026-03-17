<?php
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}
$userId = $_SESSION['user_id'];

$groupedAuctions = [
    "en_cours" => [],
    "annulees" => [],
    "terminees" => [],
    "remportees" => []
];

try {

    // DEBUG USER
     // var_dump($userId);

    $stmt = $pdo->prepare("
        SELECT DISTINCT horse_id_fk
        FROM bids
        WHERE user_id_fk = ?
    ");
    $stmt->execute([$userId]);
    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //  DEBUG BIDS
     // var_dump($bids); die();

    foreach ($bids as $bid) {

        $horseId = $bid['horse_id_fk'];

        $stmtHorse = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmtHorse->execute([$horseId]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        if (!$horse) continue;

        $stmtAuction = $pdo->prepare("
            SELECT auction_status, auction_end_date, auction_starting_price
            FROM auctions
            WHERE horse_id_fk = ?
        ");
        $stmtAuction->execute([$horseId]);
        $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

        if (!$auction) continue;

        $stmtPrice = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmtPrice->execute([$horseId]);
        $lastBid = $stmtPrice->fetchColumn();

        $currentPrice = $lastBid ?: $auction['auction_starting_price'];

        $stmtCount = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmtCount->execute([$horseId]);
        $participants = (int)$stmtCount->fetchColumn();

        $data = [
            "id_horse" => $horseId,
            "horse_name" => $horse['horse_name'],
            "auction_end_date" => $auction['auction_end_date'],
            "last_price" => $currentPrice,
            "participants" => $participants
        ];

        switch ($auction['auction_status']) {

            case 'active':
                $groupedAuctions["en_cours"][] = $data;
                break;

            case 'cancelled':
                $groupedAuctions["annulees"][] = $data;
                break;

            case 'finished':

                $stmtWinner = $pdo->prepare("
                    SELECT user_id_fk
                    FROM bids
                    WHERE horse_id_fk = ?
                    ORDER BY bid_amount DESC
                    LIMIT 1
                ");
                $stmtWinner->execute([$horseId]);
                $winnerId = $stmtWinner->fetchColumn();

                if ($winnerId == $userId) {
                    $groupedAuctions["remportees"][] = $data;
                } else {
                    $groupedAuctions["terminees"][] = $data;
                }

                break;
        }
    }

    $stmtOutbid = $pdo->prepare("
        SELECT COUNT(*) 
        FROM outbid 
        WHERE user_id_fk = ?
    ");
    $stmtOutbid->execute([$userId]);
    $outbidCount = $stmtOutbid->fetchColumn();

} catch (PDOException $e) {

    $groupedAuctions = [
        "en_cours" => [],
        "annulees" => [],
        "terminees" => [],
        "remportees" => []
    ];
    $outbidCount = 0;
}