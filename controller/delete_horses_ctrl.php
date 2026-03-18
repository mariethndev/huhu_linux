<?php
session_start();
require_once "../model/config.php";

if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: ../views/profile.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/horses_list.php");
    exit;
}

$horse_id = (int)($_POST['horse_id'] ?? 0);

if ($horse_id <= 0) {
    header("Location: ../views/horses_list.php");
    exit;
}

try {
 
    $stmt = $pdo->prepare("
        DELETE FROM auctions
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horse_id]);
 
    $stmt = $pdo->prepare("
        UPDATE horses
        SET horse_is_deleted = 1
        WHERE id_horse = ?
    ");
    $stmt->execute([$horse_id]);

    header("Location: ../views/horses_list.php");
    exit;

} catch (PDOException $e) {

    header("Location: ../views/horses_list.php");
    exit;
}