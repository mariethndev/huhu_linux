<?php
//  je  démarre la session pour récupérer l'utilisateur connecté
session_start();

//  je  charge la config (connexion bdd etc)
require_once "../model/config.php";

//  je  récupère l'id du cheval depuis l'url
$horseId = (int)($_GET['id'] ?? 0);

// si l'id est pas valide on renvoie vers la page liste
if ($horseId <= 0) {
    header("Location: ../views/buy_a_horse.php");
    exit;
}

//  je  prépare les variables
$horse = null;
$auction = [];
$userLogged = !empty($_SESSION['user_id']); // check si user connecté

try {

    //  je  récupère le cheval
    $stmt = $pdo->prepare("
        SELECT *
        FROM horses
        WHERE id_horse = ?
        LIMIT 1
    ");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    // si le cheval existe pas on redirige
    if (!$horse) {
        header("Location: ../views/buy_a_horse.php");
        exit;
    }

    //  je  récupère l'enchère liée au cheval
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE horse_id_fk = ?
        LIMIT 1
    ");
    $stmt->execute([$horseId]);

    // si y'a rien on met tableau vide
    $auctionData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // id de l'enchère
    $auctionId = $auctionData['id_auction'] ?? 0;

    //  je  prépare la dernière enchère
    $lastBid = null;

    // si y'a une enchère
    if ($auctionId) {
        //  je  récupère le plus gros montant
        $stmt = $pdo->prepare("
            SELECT MAX(bid_amount)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmt->execute([$auctionId]);
        $lastBid = $stmt->fetchColumn();
    }

    //  je  calcule le prix actuel
    if ($lastBid !== null && $lastBid > 0) {
        // si quelqu’un a déjà enchéri
        $currentPrice = (float)$lastBid;

    } elseif (!empty($auctionData['auction_starting_price'])) {
        // sinon on prend le prix de départ
        $currentPrice = (float)$auctionData['auction_starting_price'];

    } else {
        // sinon 0
        $currentPrice = 0;
    }

    // nombre de participants
    $participants = 0;

    if ($auctionId) {
        //  je  compte les users différents qui ont enchéri
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmt->execute([$auctionId]);
        $participants = (int)$stmt->fetchColumn();
    }

    //  je  récupère le statut
    $status = strtolower(trim($auctionData['auction_status'] ?? ''));

    //  je  check si la date est dépassée
    $isEnded = !empty($auctionData['auction_end_date']) &&
               strtotime($auctionData['auction_end_date']) <= time();

    // active si dispo et pas terminé
    $isActive = ($status === 'disponible' && !$isEnded);

    //  je  construit le tableau final
    $auction = [
        "id_auction"    => $auctionId,
        "is_active"     => $isActive,
        "status_label"  => $isActive ? "En cours" : "Clôturée",
        "badge_class"   => $isActive ? "bg-success" : "bg-danger",
        "current_price" => $currentPrice,
        "participants"  => $participants,
    ];

} catch (PDOException $e) {

    // en cas d'erreur bdd
    echo $e->getMessage(); // à virer en prod

    //  je  remet des valeurs safe
    $horse = null;
    $auction = [
        "id_auction" => 0,
        "is_active" => false,
        "status_label" => "Erreur",
        "badge_class" => "bg-danger",
        "current_price" => 0,
        "participants" => 0,
    ];
}