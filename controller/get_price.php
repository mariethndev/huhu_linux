<?php
require_once "../model/config.php";

$horseId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT MAX(bid_amount)
    FROM bids
    WHERE horse_id_fk = ?
");
$stmt->execute([$horseId]);

$price = $stmt->fetchColumn();

echo $price ?: 0;