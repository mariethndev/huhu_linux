<?php
require_once "../model/config.php";

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ../views/organisateur_auctions.php");
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM auctions
        WHERE id_auction = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        header("Location: ../views/organisateur_auctions.php");
        exit;
    }

    $dateValue = $auction['auction_end_date'] ?? '';

    if (empty($auction['auction_status'])) {
        $auction['auction_status'] = 'disponible';
    }

} catch (PDOException $e) {

    header("Location: ../views/organisateur_auctions.php");
    exit;
}