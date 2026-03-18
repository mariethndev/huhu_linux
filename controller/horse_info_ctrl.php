<?php
require_once "../model/config.php";

$horseId = (int)($_GET['id'] ?? 0);

if ($horseId <= 0) {
    header("Location: ../views/buy_a_horse.php");
    exit;
}

$horse = null;
$auction = [];
$userLogged = !empty($_SESSION['user_id']);

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM horses
        WHERE id_horse = ?
        AND horse_is_deleted = 0
    ");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horse) {
        header("Location: ../views/buy_a_horse.php");
        exit;
    }

        $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE horse_id_fk = ?
        LIMIT 1
    ");

    $stmt->execute([$horseId]);
    $auctionData = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT MAX(bid_amount)
        FROM bids
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horseId]);
    $lastBid = $stmt->fetchColumn();

    $currentPrice = $lastBid ?: ($auctionData['auction_starting_price'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT user_id_fk)
        FROM bids
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horseId]);
    $participants = (int)$stmt->fetchColumn();

    $horse['image_path'] = !empty($horse['horse_image'])
        ? "/huhu/huhu_linux/uploads/horses/" . $horse['horse_image']
        : "/huhu/huhu_linux/uploads/horses/horse_default.png";

    $horse['birthdate_formatted'] = !empty($horse['horse_birthdate'])
        ? date('d/m/Y', strtotime($horse['horse_birthdate']))
        : '—';

    $horse['register_date_formatted'] = !empty($horse['horse_register_date'])
        ? date('d/m/Y', strtotime($horse['horse_register_date']))
        : '—';

    $horse['height_formatted'] = !empty($horse['horse_height'])
        ? $horse['horse_height'] . ' cm'
        : 'NC';

    $horse['weight_formatted'] = !empty($horse['horse_weight'])
        ? $horse['horse_weight'] . ' kg'
        : 'NC';

    $horse['description_clean'] = (!empty($horse['horse_description']) && $horse['horse_description'] !== '...')
        ? nl2br(htmlentities($horse['horse_description']))
        : 'Aucune description disponible.';

    $status = strtolower(trim($auctionData['auction_status'] ?? ''));
    $isEnded = !empty($auctionData['auction_end_date']) &&
        strtotime($auctionData['auction_end_date']) <= time();

    $isActive = ($status === 'disponible' && !$isEnded);

    // 📦 DATA POUR LA VIEW
    $auction = [
        "is_active"      => $isActive,
        "status_label"   => $isActive ? "En cours" : "Clôturée",
        "badge_class"    => $isActive ? "bg-success" : "bg-danger",
        "current_price"  => (float)$currentPrice,
        "price_formatted"=> number_format($currentPrice, 0, ',', ' '),
        "participants"   => $participants
    ];

} catch (PDOException $e) {

    $horse = null;
    $auction = [
        "is_active" => false,
        "status_label" => "Erreur",
        "badge_class" => "bg-danger",
        "current_price" => 0,
        "price_formatted" => "0",
        "participants" => 0
    ];
}