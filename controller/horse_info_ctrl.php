<?php
// je démarre la session
session_start();

// je charge la config
require_once "../model/config.php";

// je récupère l'id cheval
$horseId = (int)($_GET['id'] ?? 0);

// si id invalide → redirection
if ($horseId <= 0) {
    header("Location: ../views/buy_a_horse.php");
    exit;
}

// variables
$horse = null;
$auction = [];
$userLogged = !empty($_SESSION['user_id']);

try {

    // je récupère le cheval
    $stmt = $pdo->prepare("SELECT * FROM horses WHERE id_horse = ? LIMIT 1");
    $stmt->execute([$horseId]);
    $horse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horse) {
        header("Location: ../views/buy_a_horse.php");
        exit;
    }

    // je récupère l'enchère
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE horse_id_fk = ? LIMIT 1");
    $stmt->execute([$horseId]);
    $auctionData = $stmt->fetch(PDO::FETCH_ASSOC);

    // si pas d'enchère → valeurs par défaut
    if (!$auctionData) {
        $auctionData = [
            'id_auction' => 0,
            'auction_starting_price' => 0,
            'auction_status' => '',
            'auction_end_date' => null
        ];
    }

    // 👉 IMPORTANT (manquait chez toi)
    $auctionId = $auctionData['id_auction'];

    // je récupère la dernière enchère
    $lastBid = 0;

    if ($auctionId) {
        $stmt = $pdo->prepare("SELECT MAX(bid_amount) FROM bids WHERE auction_id_fk = ?");
        $stmt->execute([$auctionId]);
        $lastBid = $stmt->fetchColumn() ?: 0;
    }

    // prix actuel
    if ($lastBid > 0) {
        $currentPrice = (float)$lastBid;
    } else {
        $currentPrice = (float)$auctionData['auction_starting_price'];
    }

    // participants
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

    // statut
    $status = strtolower(trim($auctionData['auction_status'] ?? ''));

    // fin enchère
    $isEnded = false;
    if (!empty($auctionData['auction_end_date'])) {
        $end = strtotime($auctionData['auction_end_date']);
        if ($end && $end <= time()) {
            $isEnded = true;
        }
    }

    // actif ?
    $isActive = ($status === 'disponible' && !$isEnded);

    // tableau final
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