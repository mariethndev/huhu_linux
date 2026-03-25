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

$userId = $_SESSION['user_id'] ?? null;

try {

    // Récupère toutes les enchères dont le statut est "disponible"
    $stmtAuctions = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE auction_status = ?
    ");
    $stmtAuctions->execute(['disponible']);
    $auctions = $stmtAuctions->fetchAll(PDO::FETCH_ASSOC);

    // Prépare une requête pour récupérer un cheval spécifique non supprimé
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

        // Récupère le montant de la plus haute enchère pour cette enchère
        $stmtLastBid = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmtLastBid->execute([$auction['id_auction']]);
        $lastBid = $stmtLastBid->fetchColumn();

        if ($lastBid !== null) {
            $currentPrice = (float)$lastBid;
        } else {
            $currentPrice = (float)$auction['auction_starting_price'];
        }

        $horse['current_price'] = $currentPrice;
        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];

        // Récupère l'utilisateur en tête de l'enchère
        $stmtLeader = $pdo->prepare("
            SELECT user_id_fk
            FROM bids
            WHERE auction_id_fk = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmtLeader->execute([$auction['id_auction']]);
        $leaderId = $stmtLeader->fetchColumn();

        $horse['is_leader'] = ($leaderId && $leaderId == $userId);


        //  Par défaut le cheval correspond aux filtres"
        $matchesFilters = true;

        // On utilise stripos pour vérifier si la valeur recherchée 
        // est contenue dans les champs (nom, race, discipline)
        // sans tenir compte des majuscules/minuscules (ex: "cheval" = "Cheval")
        // Si la valeur n'est pas trouvée, on exclut le cheval des résultats
        if ($search !== '') {
            if (stripos($horse['horse_name'], $search) === false) {
                $matchesFilters = false;
            }
        }

        if ($breed !== '') {
            if (stripos($horse['horse_breed'], $breed) === false) {
                $matchesFilters = false;
            }
        }

        if ($discipline !== '') {
            if (stripos($horse['horse_discipline'], $discipline) === false) {
                $matchesFilters = false;
            }
        }

        if ($sex === 'male') {
            if ($horse['horse_sex'] !== 'M') {
                $matchesFilters = false;
            }
        } else if ($sex === 'jument') {
            if ($horse['horse_sex'] !== 'F') {
                $matchesFilters = false;
            }
        }

        $age = null;
        if (!empty($horse['horse_birthdate'])) {
            $birthDate = new DateTime($horse['horse_birthdate']);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }

        if ($ageFilter !== '' && $age !== null) {

            if ($ageFilter === 'poulain') {
                if (!($age < 3 && $horse['horse_sex'] === 'M')) {
                    $matchesFilters = false;
                }
            } else if ($ageFilter === 'pouliche') {
                if (!($age < 3 && $horse['horse_sex'] === 'F')) {
                    $matchesFilters = false;
                }
            } else if ($ageFilter === 'jeune_adulte') {
                if (!($age >= 3 && $age < 6)) {
                    $matchesFilters = false;
                }
            } else if ($ageFilter === 'adulte') {
                if (!($age >= 6 && $age < 15)) {
                    $matchesFilters = false;
                }
            } else if ($ageFilter === 'senior') {
                if (!($age >= 15)) {
                    $matchesFilters = false;
                }
            }
        }

        if ($price_min !== '') {
            if ($currentPrice < (float)$price_min) {
                $matchesFilters = false;
            }
        }

        if ($price_max !== '') {
            if ($currentPrice > (float)$price_max) {
                $matchesFilters = false;
            }
        }

        if (!$matchesFilters) {
            continue;
        }

        $horses[] = $horse;
    }

} catch (PDOException $e) {
    echo $e->getMessage();
    $horses = [];
}

$count = count($horses);