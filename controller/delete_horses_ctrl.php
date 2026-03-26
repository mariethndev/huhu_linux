<?php
 session_start();
require_once "../model/config.php";

// je vérifie que l'utilisateur est organisateur
if (($_SESSION['role'] ?? '') !== 'organisateur') {
    header("Location: ../views/profile.php");
    exit;
}

// j'accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/horses_list.php");
    exit;
}

// je récupère l'id du cheval
$horse_id = (int)($_POST['horse_id'] ?? 0);

// si id invalide donc inférieur ou égal à 0
if ($horse_id <= 0) {
    header("Location: ../views/horses_list.php");
    exit;
}

try {
 
    // je supprime les enchères liées au cheval
    $stmt = $pdo->prepare("
        DELETE FROM auctions
        WHERE horse_id_fk = ?
    ");
    $stmt->execute([$horse_id]);
 
    // je "supprime" le cheval (soft delete)
    $stmt = $pdo->prepare("
        UPDATE horses
        SET horse_is_deleted = 1
        WHERE id_horse = ?
    ");
    $stmt->execute([$horse_id]);

    header("Location: ../views/horses_list.php");
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();
    header("Location: ../views/horses_list.php");
    exit;
}