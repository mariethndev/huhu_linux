<?php
session_start();
require_once "../model/config.php";

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'organisateur'
) {
    header("Location: ../views/homepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/add_horses_form.php");
    exit;
}

$horse_name      = trim($_POST['horse_name'] ?? '');
$horse_breed     = trim($_POST['horse_breed'] ?? '');
$horse_sex       = $_POST['horse_sex'] ?? '';
$horse_birthdate = $_POST['horse_birthdate'] ?? '';
$horse_status    = $_POST['horse_status'] ?? 'disponible';

$horse_discipline = trim($_POST['horse_discipline'] ?? '');
$horse_coat       = trim($_POST['horse_coat'] ?? '');

$horse_height = !empty($_POST['horse_height']) ? (int)$_POST['horse_height'] : null;
$horse_weight = !empty($_POST['horse_weight']) ? (int)$_POST['horse_weight'] : null;

$horse_father     = trim($_POST['horse_father'] ?? '');
$horse_mother     = trim($_POST['horse_mother'] ?? '');
$horse_id_number  = trim($_POST['horse_id_number'] ?? '');
$horse_nb_ueln    = trim($_POST['horse_nb_ueln'] ?? '');
$horse_description = trim($_POST['horse_description'] ?? '');

$auction_price = !empty($_POST['auction_starting_price'])
    ? (float)$_POST['auction_starting_price']
    : 1000;

$user_id = $_SESSION['user_id'];

$imageName = "horse_default.png";
$uploadDir = __DIR__ . "/../uploads/horses/";

if (
    isset($_FILES['horse_image']) &&
    $_FILES['horse_image']['error'] === 0
) {

    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['horse_image']['tmp_name']);

    if (!in_array($mime, $allowedTypes)) {
        exit("Format image interdit : " . $mime);
    }

    if (!is_uploaded_file($_FILES['horse_image']['tmp_name'])) {
        exit("Upload invalide");
    }

    $extension = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
        default => 'jpg'
    };

    $imageName = uniqid("horse_") . "." . $extension;
    $destination = $uploadDir . $imageName;

    move_uploaded_file($_FILES['horse_image']['tmp_name'], $destination);
}

// =====================
// VALIDATION
// =====================
if (
    empty($horse_name) ||
    empty($horse_breed) ||
    empty($horse_sex) ||
    empty($horse_birthdate)
) {
    header("Location: ../views/add_horses_form.php?status=danger");
    exit;
}

$stmt = $pdo->prepare("
    SELECT id_horse
    FROM horses
    WHERE horse_id_number = ?
");
$stmt->execute([$horse_id_number]);

if ($stmt->fetch()) {
    header("Location: ../views/add_horses_form.php?status=danger&message=Numéro déjà utilisé");
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO horses (
            horse_name,
            horse_breed,
            horse_sex,
            horse_birthdate,
            horse_status,
            horse_discipline,
            horse_coat,
            horse_height,
            horse_weight,
            horse_father,
            horse_mother,
            horse_id_number,
            horse_nb_ueln,
            horse_description,
            user_id_fk,
            horse_image
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

    $horse_id = $pdo->lastInsertId();

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

    header("Location: ../views/add_horses_form.php?status=success");
    exit;

} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}