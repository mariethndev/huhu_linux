<?php
// je charge la config (connexion bdd)
require_once "../model/config.php";

// tableau final des chevaux
$horses = [];

try {

    // je récupère toutes les enchères dispo (actives)
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE auction_status = ?
        ORDER BY auction_start_date DESC
    ");
    $stmt->execute(['disponible']);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // je boucle sur chaque enchère
    foreach ($auctions as $auction) {

        // jerécupère le cheval lié à l'enchère
        $stmtHorse = $pdo->prepare("
            SELECT *
            FROM horses
            WHERE id_horse = ?
            AND horse_is_deleted = 0
        ");
        $stmtHorse->execute([$auction['horse_id_fk']]);
        $horse = $stmtHorse->fetch(PDO::FETCH_ASSOC);

        // si le cheval existe pas on skip
        if (!$horse) continue;

        // je récupère la plus grosse enchère
        $stmtPrice = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmtPrice->execute([$auction['id_auction']]);
        $lastBid = $stmtPrice->fetchColumn();

        // je calcule le prix actuel
        $horse['current_price'] = $lastBid ? (float)$lastBid: (float)$auction['auction_starting_price'];

        // je ajoute les infos de l'enchère dans le cheval
        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];
        $horse['id_auction']         = $auction['id_auction'];

        // je ajoute au tableau final
        $horses[] = $horse;
    }

} catch (PDOException $e) {

    echo $e->getMessage(); 
    $horses = [];
}

// nombre total de chevaux récupérés
$count = count($horses);