<?php
session_start();
require_once "../model/config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../views/login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$horseId = (int)($_POST['horse_id'] ?? 0);
$userId  = (int)$_SESSION['user_id'];

if ($horseId <= 0) {
    header("Location: ../views/horse_info.php?id=$horseId");
    exit;
}

try {

    // 🔎 Vérifier enchère
    $stmtAuction = $pdo->prepare("
        SELECT auction_status, auction_end_date, auction_starting_price
        FROM auctions
        WHERE horse_id_fk = ?
        LIMIT 1
    ");
    $stmtAuction->execute([$horseId]);
    $auction = $stmtAuction->fetch(PDO::FETCH_ASSOC);

    if (
        !$auction ||
        $auction['auction_status'] !== 'disponible' ||
        strtotime($auction['auction_end_date']) <= time()
    ) {
        header("Location: ../views/horse_info.php?id=$horseId");
        exit;
    }

    // 🔎 Dernier bid
    $stmt = $pdo->prepare("
        SELECT id_bid, user_id_fk, bid_amount
        FROM bids
        WHERE horse_id_fk = ?
        ORDER BY bid_amount DESC
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $lastBid = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastBid) {
        $currentPrice  = (float)$lastBid['bid_amount'];
        $previousUser  = (int)$lastBid['user_id_fk'];
        $previousBidId = (int)$lastBid['id_bid'];
    } else {
        $currentPrice  = (float)$auction['auction_starting_price'];
        $previousUser  = null;
        $previousBidId = null;
    }

    // 🔥 incrément automatique
    $increment = 50;
    $bidAmount = $currentPrice + $increment;

    // ❌ empêcher double enchère
    if ($previousUser !== null && $previousUser === $userId) {
        header("Location: ../views/horse_info.php?id=$horseId");
        exit;
    }

    // 💰 insertion
    $stmtInsert = $pdo->prepare("
        INSERT INTO bids (user_id_fk, bid_amount, bid_date, horse_id_fk)
        VALUES (?, ?, NOW(), ?)
    ");
    $stmtInsert->execute([
        $userId,
        $bidAmount,
        $horseId
    ]);

    // 🔴 notifier ancien enchérisseur
    if ($previousUser && $previousUser !== $userId) {

        $check = $pdo->prepare("
            SELECT COUNT(*) 
            FROM outbid 
            WHERE bid_id_fk = ? AND user_id_fk = ?
        ");
        $check->execute([$previousBidId, $previousUser]);

        if ($check->fetchColumn() == 0) {

            $stmtOutbid = $pdo->prepare("
                INSERT INTO outbid (bid_id_fk, user_id_fk, seen)
                VALUES (?, ?, 0)
            ");
            $stmtOutbid->execute([
                $previousBidId,
                $previousUser
            ]);
        }
    }

    $stmtClean = $pdo->prepare("
        UPDATE outbid
        SET seen = 1
        WHERE user_id_fk = ?
        AND bid_id_fk IN (
            SELECT id_bid FROM bids WHERE horse_id_fk = ?
        )
    ");
    $stmtClean->execute([$userId, $horseId]);

    $stmtUpdate = $pdo->prepare("
        UPDATE auctions
        SET auction_final_price = ?
        WHERE horse_id_fk = ?
    ");
    $stmtUpdate->execute([
        $bidAmount,
        $horseId
    ]);

    header("Location: ../views/horse_info.php?id=$horseId");
    exit;

} catch (PDOException $e) {

    // debug si besoin
    // echo $e->getMessage();

    header("Location: ../views/horse_info.php?id=$horseId");
    exit;
}