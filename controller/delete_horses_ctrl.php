<?php
session_start();
require_once "../model/config.php";
if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/profile.php?status=danger");
    exit;
} 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/horses_list.php");
    exit;
}


if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
     !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header("Location: ../views/horses_list.php?status=danger");
    exit;
} 
$horse_id = $_POST['horse_id'] ?? null;

if (!$horse_id || !is_numeric($horse_id)) {
    header("Location: ../views/horses_list.php?status=danger");
    exit;
}

$horse_id = (int)$horse_id;


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

    header("Location: ../views/horses_list.php?status=success");
    exit;

} catch (PDOException $e) {
    header("Location: ../views/horses_list.php?status=danger");
    exit;
}