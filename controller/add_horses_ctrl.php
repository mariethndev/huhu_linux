<?php
// je démarre la session
session_start();

// je charge la config bdd
require_once "../model/config.php";

// je vérifie que je suis connecté et organisateur
if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

// j’accepte uniquement le POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/add_horses_form.php");
    exit;
}

// je récupère les champs du formulaire
$horse_name      = trim($_POST['horse_name'] ?? '');
$horse_breed     = trim($_POST['horse_breed'] ?? '');
$horse_sex       = $_POST['horse_sex'] ?? '';
$horse_birthdate = $_POST['horse_birthdate'] ?? '';
$horse_status    = $_POST['horse_status'] ?? 'disponible';

$horse_discipline = trim($_POST['horse_discipline'] ?? '');
$horse_coat       = trim($_POST['horse_coat'] ?? '');

// j’ai des champs optionnels
$horse_height = !empty($_POST['horse_height']) ? (int)$_POST['horse_height'] : null;
$horse_weight = !empty($_POST['horse_weight']) ? (int)$_POST['horse_weight'] : null;

$horse_father     = trim($_POST['horse_father'] ?? '');
$horse_mother     = trim($_POST['horse_mother'] ?? '');
$horse_id_number  = trim($_POST['horse_id_number'] ?? '');
$horse_nb_ueln    = trim($_POST['horse_nb_ueln'] ?? '');
$horse_description = trim($_POST['horse_description'] ?? '');

// je définis le prix de départ (1000 par défaut)
$auction_price = !empty($_POST['auction_starting_price'])
    ? (float)$_POST['auction_starting_price']
    : 1000;

// je récupère mon id user
$user_id = $_SESSION['user_id'];

// j’ai une image par défaut
$imageName = "horse_default.png";
$uploadDir = __DIR__ . "/../uploads/horses/";

// je crée le dossier si il existe pas
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// je vérifie que le dossier est writable
if (!is_writable($uploadDir)) {
    error_log("Dossier non writable: " . $uploadDir);
    header("Location: ../views/add_horses_form.php?status=error_upload");
    exit;
}

// je gère l’upload de l’image
if (
    isset($_FILES['horse_image']) &&
    $_FILES['horse_image']['error'] === 0
) {

    // j’autorise certains formats
    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif'
    ];

    // je récupère le vrai type mime
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['horse_image']['tmp_name']);

    // si le format est interdit j’arrête
    if (!in_array($mime, $allowedTypes)) {
        header("Location: ../views/add_horses_form.php?status=invalid_format");
        exit;
    }

    // je vérifie que le fichier vient bien d’un upload
    if (!is_uploaded_file($_FILES['horse_image']['tmp_name'])) {
        header("Location: ../views/add_horses_form.php?status=invalid_upload");
        exit;
    }

    // je détermine l’extension
    $extension = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
        default => 'jpg'
    };

    // je génère un nom unique
    $imageName = uniqid("horse_") . "." . $extension;
    $destination = $uploadDir . $imageName;

    // je déplace le fichier
    if (!move_uploaded_file($_FILES['horse_image']['tmp_name'], $destination)) {
        error_log("Erreur upload vers : " . $destination);
        header("Location: ../views/add_horses_form.php?status=upload_failed");
        exit;
    }
}

// je vérifie les champs obligatoires
if (
    empty($horse_name) ||
    empty($horse_breed) ||
    empty($horse_sex) ||
    empty($horse_birthdate)
) {
    header("Location: ../views/add_horses_form.php?status=danger");
    exit;
}

// je vérifie que le numéro est unique
$stmt = $pdo->prepare("
    SELECT id_horse FROM horses WHERE horse_id_number = ?
");
$stmt->execute([$horse_id_number]);

if ($stmt->fetch()) {
    header("Location: ../views/add_horses_form.php?status=danger&message=Numéro déjà utilisé");
    exit;
}

try {

    // j’insère le cheval
    $stmt = $pdo->prepare("
        INSERT INTO horses (
            horse_name, horse_breed, horse_sex, horse_birthdate,
            horse_status, horse_discipline, horse_coat,
            horse_height, horse_weight,
            horse_father, horse_mother,
            horse_id_number, horse_nb_ueln,
            horse_description, user_id_fk, horse_image
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $horse_name,
        $horse_breed,
        $horse_sex,
        $horse_birthdate,
        $horse_status,
        $horse_discipline,
        $horse_coat,
        $horse_height,
        $horse_weight,
        $horse_father,
        $horse_mother,
        $horse_id_number,
        $horse_nb_ueln,
        $horse_description,
        $user_id,
        $imageName
    ]);

    // je récupère l’id du cheval
    $horse_id = $pdo->lastInsertId();

    // si le cheval est dispo je crée une enchère
    if ($horse_status === 'disponible') {

        $stmtAuction = $pdo->prepare("
            INSERT INTO auctions (
                auction_starting_price,
                auction_start_date,
                auction_end_date,
                horse_id_fk,
                auction_status
            )
            VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), ?, ?)
        ");

        $stmtAuction->execute([
            $auction_price,
            $horse_id,
            'disponible'
        ]);
    }

    // redirection en cas de succès
    header("Location: ../views/add_horses_form.php?status=success");
    exit;

} catch (PDOException $e) {

    echo $e->getMessage();
    header("Location: ../views/add_horses_form.php?status=error_db");
    exit;
}