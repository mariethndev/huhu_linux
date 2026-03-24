<?php

function afficherTexteSecurise($valeur) {
    return htmlentities($valeur ?? '', ENT_QUOTES, 'UTF-8');
}

$input = "<script>alert('hack')</script>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test sécurité</title>
</head>
<body>

<h2>Test sans sécurité </h2>
<p><?= $input ?></p>

<h2>Test avec sécurité</h2>
<p><?= afficherTexteSecurise($input) ?></p>

</body>
</html>