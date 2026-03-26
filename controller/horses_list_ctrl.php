<?php
//  je  charge la config (connexion bdd)
require_once "../model/config.php";

// tableau final des chevaux
$horses = [];

try {

    //  je  récupère toutes les enchères dispo
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE auction_status = ?
        ORDER BY auction_start_date DESC
    ");
    $stmt->execute(['disponible']);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //  je  boucle sur chaque enchère
    foreach ($auctions as $auction) {

        //  je  récupère le cheval lié
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

        //  je  prend le prix final de l'enchère
        $currentPrice = $auction['auction_final_price'];

        // si pas encore de prix final → on prend le prix de départ
        if ($currentPrice === null) {
            $currentPrice = $auction['auction_starting_price'];
        }

        //  je  cast en float pour être propre
        $horse['current_price'] = (float)$currentPrice;

        //  je  ajoute les infos de l'enchère
        $horse['auction_start_date'] = $auction['auction_start_date'];
        $horse['auction_end_date']   = $auction['auction_end_date'];
        $horse['id_auction']         = $auction['id_auction'];

        //  je  ajoute au tableau final
        $horses[] = $horse;
    }

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage(); // à virer en prod

    // fallback vide
    $horses = [];
}

// nombre total de chevaux
$count = count($horses);