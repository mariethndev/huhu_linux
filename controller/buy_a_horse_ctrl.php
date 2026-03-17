<?php
require_once "../model/config.php";

$horses = [];

$search     = trim($_GET['search'] ?? '');
$breed      = trim($_GET['breed'] ?? '');
$discipline = trim($_GET['discipline'] ?? '');
$sex        = $_GET['filter_sex'] ?? '';
$ageFilter  = $_GET['filter_age'] ?? '';
$price_min  = $_GET['price_min'] ?? '';
$price_max  = $_GET['price_max'] ?? '';

try {

    $stmt = $pdo->query("
        SELECT *
        FROM auctions
        WHERE auction_status = 'disponible'
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

        if (!$horse) {
            continue;
        }

        $stmtPrice = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE horse_id_fk = ?
        ");

        $stmtPrice->execute([$horse['id_horse']]);
        $lastBid = $stmtPrice->fetchColumn();

        $horse['current_price'] = $lastBid ?: $auction['auction_starting_price'];
        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];

        if ($search !== '' && stripos($horse['horse_name'] ?? '', $search) === false) {
            continue;
        }

        if ($breed !== '' && stripos($horse['horse_breed'] ?? '', $breed) === false) {
            continue;
        }

        if ($discipline !== '' && stripos($horse['horse_discipline'] ?? '', $discipline) === false) {
            continue;
        }

        if ($sex === 'male' && ($horse['horse_sex'] ?? '') !== 'M') {
            continue;
        }

        if ($sex === 'jument' && ($horse['horse_sex'] ?? '') !== 'F') {
            continue;
        }

        $age = null;

        if (!empty($horse['horse_birthdate'])) {
            $birthDate = new DateTime($horse['horse_birthdate']);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }

        if ($ageFilter !== '' && $age !== null) {

            if ($ageFilter === 'poulain' && !($age < 3 && ($horse['horse_sex'] ?? '') === 'M')) {
                continue;
            }

            if ($ageFilter === 'pouliche' && !($age < 3 && ($horse['horse_sex'] ?? '') === 'F')) {
                continue;
            }

            if ($ageFilter === 'jeune_adulte' && !($age >= 3 && $age < 6)) {
                continue;
            }

            if ($ageFilter === 'adulte' && !($age >= 6 && $age < 15)) {
                continue;
            }

            if ($ageFilter === 'senior' && !($age >= 15)) {
                continue;
            }
        } 

        if ($price_min !== '' && $horse['current_price'] < (float)$price_min) {
            continue;
        }
 
        if ($price_max !== '' && $horse['current_price'] > (float)$price_max) {
            continue;
        }

        $horses[] = $horse;
    }

} catch (PDOException $e) {
    $horses = [];
}

$count = count($horses);