<?php
require_once "../model/config.php";

$horses = [];
$count  = 0;

$search     = $_GET['search'] ?? '';
$breed      = $_GET['breed'] ?? '';
$sex        = $_GET['filter_sex'] ?? '';
$ageFilter  = $_GET['filter_age'] ?? '';
$price_min  = $_GET['price_min'] ?? '';
$price_max  = $_GET['price_max'] ?? '';

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
        FROM auctions
        WHERE auction_status = 'disponible'
        AND (auction_end_date IS NULL OR auction_end_date > NOW())
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

        if ($search !== '') {
            if (stripos($horse['horse_name'] ?? '', $search) === false) {
                continue;
            }
        }

        if ($breed !== '') {
            if (stripos($horse['horse_breed'] ?? '', $breed) === false) {
                continue;
            }
        }

        if ($sex === "male" && ($horse['horse_sex'] ?? '') !== "M") {
            continue;
        }

        if ($sex === "jument" && ($horse['horse_sex'] ?? '') !== "F") {
            continue;
        }

        $age = null;

        if (!empty($horse['horse_birthdate'])) {
            $birth = new DateTime($horse['horse_birthdate']);
            $today = new DateTime();
            $age = $today->diff($birth)->y;
        }

        if ($ageFilter !== '' && $age !== null) {

            if ($ageFilter === "poulain" && !($age < 3 && $horse['horse_sex'] === "M")) {
                continue;
            }

            if ($ageFilter === "pouliche" && !($age < 3 && $horse['horse_sex'] === "F")) {
                continue;
            }

            if ($ageFilter === "jeune_adulte" && !($age >= 3 && $age < 6)) {
                continue;
            }

            if ($ageFilter === "adulte" && !($age >= 6 && $age < 15)) {
                continue;
            }

            if ($ageFilter === "senior" && !($age >= 15)) {
                continue;
            }
        }

        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id = ?
        ");
        $stmt->execute([$horse['id_horse']]);
        $lastBid = $stmt->fetchColumn();

        $horse['current_price'] = $lastBid ?: $auction['auction_starting_price'];

        if ($price_min !== '' && $horse['current_price'] < (float)$price_min) {
            continue;
        }

        if ($price_max !== '' && $horse['current_price'] > (float)$price_max) {
            continue;
        }

        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];

        $horses[] = $horse;
    }

} catch (PDOException $e) {
    $horses = [];
}

$count = count($horses);