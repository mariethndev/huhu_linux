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

$outbids = [];
$outbidCount = 0;

try {

    // ===============================
    // 🔔 OUTBIDS (SANS ALIAS)
    // ===============================
    $stmt = $pdo->prepare("
        SELECT DISTINCT bids.horse_id_fk, horses.horse_name
        FROM outbid
        JOIN bids ON outbid.bid_id_fk = bids.id_bid
        JOIN horses ON bids.horse_id_fk = horses.id_horse
        WHERE outbid.user_id_fk = ? AND outbid.seen = 0
    ");
    $stmt->execute([$userId]);
    $outbids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $outbidCount = count($outbids);

    // ===============================
    // 🔎 CHEVAUX SUR LESQUELS J'AI MISÉ
    // ===============================
    $stmt = $pdo->prepare("
        SELECT DISTINCT horse_id_fk
        FROM bids
        WHERE user_id_fk = ?
    ");
    $stmt->execute([$userId]);
    $horses = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($horses as $horseId) {

        // CHEVAL
        $stmt = $pdo->prepare("
            SELECT horse_name
            FROM horses
            WHERE id_horse = ?
        ");
        $stmt->execute([$horseId]);
        $horse = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$horse) continue;

        // AUCTION
        $stmt = $pdo->prepare("
            SELECT auction_status, auction_end_date, auction_starting_price
            FROM auctions
            WHERE horse_id_fk = ?
        ");
        $stmt->execute([$horseId]);
        $auction = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$auction) continue;

        // PRIX ACTUEL
        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmt->execute([$horseId]);
        $currentPrice = $stmt->fetchColumn() ?: $auction['auction_starting_price'];

        // MON ENCHÈRE
        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id_fk = ? AND user_id_fk = ?
        ");
        $stmt->execute([$horseId, $userId]);
        $myLastBid = $stmt->fetchColumn();

        // MON ID BID
        $stmt = $pdo->prepare("
            SELECT id_bid
            FROM bids
            WHERE horse_id_fk = ? AND user_id_fk = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmt->execute([$horseId, $userId]);
        $myBidId = $stmt->fetchColumn();

        // 🔴 EST-CE QUE JE SUIS DÉPASSÉ ?
        $isOutbid = false;

        if ($myBidId) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM outbid
                WHERE user_id_fk = ? AND bid_id_fk = ? AND seen = 0
            ");
            $stmt->execute([$userId, $myBidId]);
            $isOutbid = $stmt->fetchColumn() > 0;
        }

        // PARTICIPANTS
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE horse_id_fk = ?
        ");
        $stmt->execute([$horseId]);
        $participants = (int)$stmt->fetchColumn();

        // DERNIER ENCHÉRISSEUR
        $stmt = $pdo->prepare("
            SELECT users.user_name, users.id_user
            FROM bids
            JOIN users ON bids.user_id_fk = users.id_user
            WHERE bids.horse_id_fk = ?
            ORDER BY bids.bid_amount DESC
            LIMIT 1
        ");
        $stmt->execute([$horseId]);
        $winner = $stmt->fetch(PDO::FETCH_ASSOC);

        $lastBidder = $winner['user_name'] ?? 'Aucun';
        $winnerId   = $winner['id_user'] ?? null;

        // DATA
        $data = [
            "id_horse"        => $horseId,
            "horse_name"      => $horse['horse_name'],
            "auction_end_date"=> $auction['auction_end_date'],
            "last_price"      => $currentPrice,
            "my_last_bid"     => $myLastBid,
            "participants"    => $participants,
            "is_outbid"       => $isOutbid,
            "last_bidder"     => $lastBidder
        ];

        // STATUS
        $status = strtolower(trim($auction['auction_status']));

        if ($status === 'disponible') {
            $groupedAuctions["en_cours"][] = $data;

        } elseif ($status === 'cancelled') {
            $groupedAuctions["annulees"][] = $data;

        } elseif ($status === 'finished') {

            if ($winnerId == $userId) {
                $groupedAuctions["remportees"][] = $data;
            } else {
                $groupedAuctions["terminees"][] = $data;
            }
        }
    }

} catch (PDOException $e) {

    $groupedAuctions = [
        "en_cours" => [],
        "annulees" => [],
        "terminees" => [],
        "remportees" => []
    ];

    $outbids = [];
    $outbidCount = 0;
}