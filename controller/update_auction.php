<?php
//  je  charge la config bdd
require_once "../model/config.php";

//  je  vérifie que l'utilisateur est connecté ET organisateur
if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

//  je  récupère l'id de l'enchère depuis l'url
$id = (int)($_GET['id'] ?? 0);

// si id invalide → retour liste
if ($id <= 0) {
    header("Location: ../views/organisateur_auctions.php");
    exit;
}

try {

    //  je  récupère l'enchère
    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE id_auction = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    // si l'enchère existe pas → retour liste
    if (!$auction) {
        header("Location: ../views/organisateur_auctions.php");
        exit;
    }

    //  je  récupère la date de fin pour le formulaire
    $dateValue = $auction['auction_end_date'] ?? '';

    // si le statut est vide → on met "disponible" par défaut
    if (empty($auction['auction_status'])) {
        $auction['auction_status'] = 'disponible';
    }

} catch (PDOException $e) {

    // erreur bdd
    echo $e->getMessage();

    // fallback → retour liste
    header("Location: ../views/organisateur_auctions.php");
    exit;
}