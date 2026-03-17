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

    $dateValue = '';

    if (!empty($auction['auction_end_date'])) {
        $timestamp = strtotime($auction['auction_end_date']);

        if ($timestamp !== false) {
            $dateValue = date('Y-m-d', $timestamp);
        }
    }

    $timeRemaining = '—';

    if (!empty($auction['auction_end_date'])) {

        $end = strtotime($auction['auction_end_date']);

        if ($end !== false) {

            $now = time();

            if ($end > $now) {

                $remaining = $end - $now;

                $days    = floor($remaining / 86400);
                $hours   = floor(($remaining % 86400) / 3600);
                $minutes = floor(($remaining % 3600) / 60);
                $seconds = $remaining % 60;

                if ($days > 0) {

                    $timeRemaining =
                        $days . "j " .
                        str_pad($hours, 2, "0", STR_PAD_LEFT) . "h " .
                        str_pad($minutes, 2, "0", STR_PAD_LEFT) . "m";

                } else {

                    $timeRemaining =
                        str_pad($hours, 2, "0", STR_PAD_LEFT) . "h " .
                        str_pad($minutes, 2, "0", STR_PAD_LEFT) . "m " .
                        str_pad($seconds, 2, "0", STR_PAD_LEFT) . "s";
                }

            } else {

                $timeRemaining = "Enchère terminée";
            }
        }
    }

    if (empty($auction['auction_status'])) {
        $auction['auction_status'] = 'disponible';
    }

} catch (PDOException $e) {

    header("Location: ../views/organisateur_auctions.php");
    exit;
}