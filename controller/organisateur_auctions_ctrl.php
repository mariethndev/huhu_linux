<?php
//  je  charge la config bdd
require_once "../model/config.php";

// tableau final des enchères
$auctions = [];

try {

    //  je  met à jour les enchères terminées automatiquement
    $stmtUpdate = $pdo->prepare("
        UPDATE auctions
        SET auction_status = ?
        WHERE auction_status = ?
        AND auction_end_date <= NOW()
    ");
    $stmtUpdate->execute(['terminé', 'disponible']);

    //  je  récupère les enchères encore dispo avec le nom du cheval
    $stmt = $pdo->prepare("
        SELECT 
            auctions.*,
            horses.horse_name
        FROM auctions
        LEFT JOIN horses ON horses.id_horse = auctions.horse_id_fk
        WHERE auctions.auction_status = ?
        ORDER BY auction_start_date DESC
    ");
    $stmt->execute(['disponible']);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // tableau pour éviter les doublons de chevaux
    $seen = [];

    //  je  boucle sur les résultats
    foreach ($results as $auction) {

        $horseId = $auction['horse_id_fk'];

        // si pas de cheval → skip
        if (!$horseId) continue;

        // si déjà traité → skip
        if (isset($seen[$horseId])) continue;

        //  je  marque comme déjà vu
        $seen[$horseId] = true;

        //  je  récupère la meilleure enchère (la plus haute)
        $stmtBid = $pdo->prepare("
            SELECT bid_amount, user_id_fk
            FROM bids
            WHERE auction_id_fk = ?
            ORDER BY bid_amount DESC
            LIMIT 1
        ");
        $stmtBid->execute([$auction['id_auction']]);
        $lastBid = $stmtBid->fetch(PDO::FETCH_ASSOC);

        if ($lastBid) {
            // si y'a une enchère → on prend le montant
            $auction['last_bid'] = $lastBid['bid_amount'];
            $winnerId = $lastBid['user_id_fk'];
        } else {
            // sinon prix de départ
            $auction['last_bid'] = $auction['auction_starting_price'];
            $winnerId = null;
        }

        // si on a un gagnant
        if ($winnerId) {

            //  je  récupère son nom
            $stmtUser = $pdo->prepare("
                SELECT user_name
                FROM users
                WHERE id_user = ?
            ");
            $stmtUser->execute([$winnerId]);

            // fallback si null
            $auction['last_bidder_name'] = $stmtUser->fetchColumn() ?: '—';

        } else {
            // aucun enchérisseur
            $auction['last_bidder_name'] = '—';
        }

        //  je  ajoute au tableau final
        $auctions[] = $auction;
    }

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback vide
    $auctions = [];
}