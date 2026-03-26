<?php
require_once "../model/config.php";

// je prépare un tableau pour stocker les chevaux filtrés
$horses = [];

// je récupère les filtres envoyés en GET (ou valeur vide par défaut)
$search     = trim($_GET['search'] ?? '');
$breed      = trim($_GET['breed'] ?? '');
$discipline = trim($_GET['discipline'] ?? '');
$sex        = $_GET['filter_sex'] ?? '';
$ageFilter  = $_GET['filter_age'] ?? '';
$price_min  = $_GET['price_min'] ?? '';
$price_max  = $_GET['price_max'] ?? '';

// je récupère l'id de l'utilisateur connecté (ou null)
$userId = $_SESSION['user_id'] ?? '';

try {

    // je récupère toutes les enchères disponibles
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE auction_status = ?");
    $stmt->execute(['disponible']);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // je parcours les enchères
    foreach ($auctions as $auction) {

        // je récupère le cheval
        $stmt = $pdo->prepare("SELECT * FROM horses WHERE id_horse = ? AND horse_is_deleted = 0");
        $stmt->execute([$auction['horse_id_fk']]);
        $horse = $stmt->fetch(PDO::FETCH_ASSOC);

        // je vérifie que le cheval existe
        if ($horse) {

            // je corrige l'image
            $image = $horse['horse_image'] ?? 'horse_default.png';
            $filePath = __DIR__ . "/../uploads/horses/" . $image;

            if (!file_exists($filePath)) {
                $image = "horse_default.png";
            }

            $horse['horse_image'] = $image;

            // je récupère le prix
            $stmt = $pdo->prepare("SELECT MAX(bid_amount) FROM bids WHERE auction_id_fk = ?");
            $stmt->execute([$auction['id_auction']]);
            $price = $stmt->fetchColumn();

            if (!$price) {
                $price = $auction['auction_starting_price'];
            }

            // je récupère le leader
            $stmt = $pdo->prepare("
                SELECT user_id_fk FROM bids 
                WHERE auction_id_fk = ? 
                ORDER BY bid_amount DESC LIMIT 1
            ");
            $stmt->execute([$auction['id_auction']]);
            $leaderId = $stmt->fetchColumn();

            // j’ajoute les infos utiles
            $horse['current_price'] = (float)$price;
            $horse['auction_start_date'] = $auction['auction_start_date'];
            $horse['auction_end_date']   = $auction['auction_end_date'];
            $horse['is_leader'] = ($leaderId && $leaderId == $userId);

            // je calcule l’âge
            $age = !empty($horse['horse_birthdate'])
                ? (new DateTime())->diff(new DateTime($horse['horse_birthdate']))->y
                : null;

            // 🔥 FIX DISCIPLINE + filtres
            $matchesFilters =
                (!$search || stripos($horse['horse_name'], $search) !== false) &&
                (!$breed || stripos($horse['horse_breed'], $breed) !== false) &&
                (
                    !$discipline ||
                    strtolower(trim($horse['horse_discipline'] ?? '')) === strtolower(trim($discipline))
                ) &&
                (
                    !$sex ||
                    ($sex === 'male' && ($horse['horse_sex'] ?? '') === 'M') ||
                    ($sex === 'jument' && ($horse['horse_sex'] ?? '') === 'F')
                ) &&
                (
                    !$ageFilter ||
                    ($age !== null && (
                        ($ageFilter === 'poulain' && $age < 3) ||
                        ($ageFilter === 'pouliche' && $age < 3) ||
                        ($ageFilter === 'jeune_adulte' && $age >= 3 && $age < 6) ||
                        ($ageFilter === 'adulte' && $age >= 6 && $age < 15) ||
                        ($ageFilter === 'senior' && $age >= 15)
                    ))
                ) &&
                ($price_min === '' || $price >= (float)$price_min) &&
                ($price_max === '' || $price <= (float)$price_max);

            // si OK → j'ajoute
            if ($matchesFilters) {
                $horses[] = $horse;
            }
        }
    }

} catch (PDOException $e) {

    echo $e->getMessage();
    $horses = [];
}

// je compte le nombre de chevaux trouvés
$count = count($horses);