<?php
// je démarre la session pour récupérer les infos utilisateur
session_start();
require_once "../model/config.php";

// je récupère l'id depuis l'URL (ex: ?id=5)
// ?? 0 → si l'id n'existe pas → je mets 0 par défaut
// (évite une erreur si rien n'est envoyé)
// (int) → je force la valeur en nombre entier
// même si quelqu’un envoie "abc" → ça devient 0
$horseId = (int)($_GET['id'] ?? 0);

// si l'id est invalide → je redirige vers la liste
if ($horseId <= 0) {
    header("Location: ../views/buy_a_horse.php");
    exit;
}

// je crée mes variables de base pour éviter les erreurs
$horse = null; //je mets null au début QUI sera rempli avec les données du cheval
$auction = []; // je crée un tableau vide QUI sera rempli avec les infos de l’enchère
$userLogged = !empty($_SESSION['user_id']); // je vérifie si l'utilisateur est connecté
// je vérifie si l'utilisateur est connecté
// si user_id existe → true
// sinon → false

try {

    // RECUPERE DU CHEVAL
    // je récupère les données du cheval
    $stmt = $pdo->prepare("SELECT * FROM horses WHERE id_horse = ? LIMIT 1");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    // si le cheval n'existe pas dans la bdd je redirige l'utilisateur sur buy a horse.
    if (!$horse) {
        header("Location: ../views/buy_a_horse.php");
        exit;
    }

    // RECUPERE LENCHERE LIEE AU CHEVAL 
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE horse_id_fk = ? LIMIT 1");
    $stmt->execute([$horseId]);
    $auctionData = $stmt->fetch(PDO::FETCH_ASSOC);

    // si aucune enchère existe pour ce cheval
    // je crée des valeurs par défaut pour éviter les erreurs
    // comme ça je peux quand même utiliser $auctionData

    if (!$auctionData) {
        $auctionData = [
            'id_auction' => 0,
            'auction_starting_price' => 0,
            'auction_status' => '',
            'auction_end_date' => null
        ];
    }
    // __ Je mets des valeurs par défaut pour éviter les erreurs quand il n’y a pas d’enchère.

    // je récupère l'id de l'enchère sans risque d'erreur
    $auctionId = $auctionData['id_auction'];

    // PARTIE PRIX DE L'ENCHERE 
    // je récupère le prix de la dernière enchère faite par l'utilisateur
    $lastBid = 0;

    if ($auctionId) {
        $stmt = $pdo->prepare("SELECT MAX(bid_amount) FROM bids WHERE auction_id_fk = ?");
        $stmt->execute([$auctionId]);
        $lastBid = $stmt->fetchColumn() ?: 0;   // le 0 remplace null renvoyé par MAX quand il n’y a aucune enchère
                                                // ça évite les erreurs et permet de continuer le calcul
    }

    // si une enchère existe et que le montant est > à 0
    // on prend ce prix
    // sinon on garde le prix de départ
    if ($lastBid > 0) {
        $currentPrice = (float)$lastBid;
    } else {
        $currentPrice = (float)$auctionData['auction_starting_price'];
    }

    // PARTIE PARTICIPANTS
    // je compte le nombre de participants (personnes différentes)
    $participants = 0;

    if ($auctionId) {
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT user_id_fk)
            FROM bids
            WHERE auction_id_fk = ?
        ");
        $stmt->execute([$auctionId]);
        $participants = (int)$stmt->fetchColumn();
    }

    // PARTIE STATUT DE L'ENCHÈRE
    // je récupère le statut enregistré en base (ex: disponible, terminé)
    // je le nettoie pour éviter les erreurs (espaces, majuscules)
    $status = strtolower(trim($auctionData['auction_status'] ?? ''));

    // je détermine le status de
    $isActive = ($status === 'disponible');

    // je prépare les données finales pour l'affichage
    // (texte, couleur, prix, participants…)
    $auction = [
        "id_auction"    => $auctionId,
        "is_active"     => $isActive,
        "status_label"  => $isActive ? "En cours" : "Clôturée",
        "badge_class"   => $isActive ? "bg-success" : "bg-danger",
        "current_price" => $currentPrice,
        "participants"  => $participants,
    ];

} catch (PDOException $e) {
    echo $e->getMessage();

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