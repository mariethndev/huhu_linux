<?php
// je démarre la session
session_start();

// je charge la config bdd
require_once "../model/config.php";

// je vérifie que l'utilisateur est organisateur
if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

// je vérifie que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/add_horses_form.php");
    exit;
}

// je récupère les données du formulaire
$horse_name      = trim($_POST['horse_name'] ?? '');
$horse_breed     = trim($_POST['horse_breed'] ?? '');
$horse_sex       = $_POST['horse_sex'] ?? '';
$horse_birthdate = $_POST['horse_birthdate'] ?? '';
$horse_status    = $_POST['horse_status'] ?? 'disponible';

$horse_height = (int)($_POST['horse_height'] ?? 0);
$horse_weight = (int)($_POST['horse_weight'] ?? 0);

$horse_description = trim($_POST['horse_description'] ?? '');
$user_id = $_SESSION['user_id'];

$auction_price = (float)($_POST['auction_starting_price'] ?? 1000);

// je définis le dossier d’upload
$uploadDir = __DIR__ . "/../uploads/horses/";
$imageName = "horse_default.png";

// je crée le dossier si besoin
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// je prépare la destination
$destination = null;

// je gère l’upload de l’image
if (isset($_FILES['horse_image']) && $_FILES['horse_image']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['horse_image']['error'] === UPLOAD_ERR_INI_SIZE) {
        header("Location: ../views/add_horses_form.php?status=danger&message=fichier trop gros");
        exit;
    }

    if ($_FILES['horse_image']['error'] !== UPLOAD_ERR_OK) {
        header("Location: ../views/add_horses_form.php?status=danger&message=upload error");
        exit;
    }

    if ($_FILES['horse_image']['size'] > 5 * 1024 * 1024) {
        header("Location: ../views/add_horses_form.php?status=danger&message=fichier trop gros");
        exit;
    }

    // je vérifie le type MIME
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['horse_image']['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif'
    ];

    if (!isset($allowed[$mime])) {
        header("Location: ../views/add_horses_form.php?status=danger&message=format invalide");
        exit;
    }

    // je génère un nom unique
    $ext = $allowed[$mime];
    $imageName = uniqid("horse_", true) . "." . $ext;
    $destination = $uploadDir . $imageName;

    // je déplace le fichier
    $uploadSuccess = false;

    if (@move_uploaded_file($_FILES['horse_image']['tmp_name'], $destination)) {
        $uploadSuccess = true;
    } elseif (@copy($_FILES['horse_image']['tmp_name'], $destination)) {
        $uploadSuccess = true;
    }

    // si échec → image par défaut
    if (!$uploadSuccess) {
        $imageName = "horse_default.png";
    }
}

// je vérifie les champs obligatoires
if (
    empty($horse_name) ||
    empty($horse_breed) ||
    empty($horse_sex) ||
    empty($horse_birthdate)
) {
    header("Location: ../views/add_horses_form.php?status=danger&message=champs manquants");
    exit;
}

try {

    // j’insère le cheval
    $stmt = $pdo->prepare("
        INSERT INTO horses (
            horse_name, horse_breed, horse_sex, horse_birthdate,
            horse_status, horse_height, horse_weight,
            horse_description, user_id_fk, horse_image
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $horse_name,
        $horse_breed,
        $horse_sex,
        $horse_birthdate,
        $horse_status,
        $horse_height,
        $horse_weight,
        $horse_description,
        $user_id,
        $imageName
    ]);

    // je crée une enchère si dispo
    if ($horse_status === 'disponible') {

        $stmt = $pdo->prepare("
            INSERT INTO auctions (
                auction_starting_price,
                auction_start_date,
                auction_end_date,
                horse_id_fk,
                auction_status
            )
            VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), ?, ?)
        ");

        $stmt->execute([
            $auction_price,
            $pdo->lastInsertId(),
            'disponible'
        ]);
    }

    header("Location: ../views/add_horses_form.php?status=success");
    exit;

} catch (PDOException $e) {

    header("Location: ../views/add_horses_form.php?status=danger&message=" . $e->getMessage());
    exit;
}