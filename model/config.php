<?php

if (session_status() === PHP_SESSION_NONE){
    session_start();
}

try {

    $pdo = new PDO(
        "mysql:host=db;dbname=huhu;charset=utf8",
        "root",
        "1234"
    );

    // afficher les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erreur DB : " . $e->getMessage());
}