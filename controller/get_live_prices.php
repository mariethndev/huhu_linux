<?php
require_once "../model/config.php";

$input = json_decode(file_get_contents("php://input"), true);
$horseId = isset($input['horse_id']) ? (int)$input['horse_id'] : 0;

if ($horseId <= 0) {
    echo json_encode(["success" => false]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT MAX(bid_amount) AS current_price
        FROM bids
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horseId]);
    $price = $stmt->fetchColumn();

    if ($price === null) {
        $stmtAuction = $pdo->prepare("
            SELECT auction_starting_price
            FROM auctions
            WHERE horse_id_fk = ?
            LIMIT 1
        ");
        $stmtAuction->execute([$horseId]);
        $price = $stmtAuction->fetchColumn() ?? 0;
    }

    echo json_encode([
        "success" => true,
        "current_price" => (float)$price
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false]);
}