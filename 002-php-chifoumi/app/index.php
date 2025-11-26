<?php


$choices = ["pierre", "feuille", "ciseaux"];

$playerChoice = $_GET['player'] ?? '';
$phpChoice = '';
$result = '';


if ($playerChoice !== '') {


    if (!in_array($playerChoice, $choices, true)) {

        $result = "Choix invalide.";

    } else {


        $phpChoice = $choices[array_rand($choices)];


        if ($playerChoice === $phpChoice) {

            $result = "Égalité";

        } elseif (
                ($playerChoice === "pierre" && $phpChoice === "ciseaux") ||
                ($playerChoice === "feuille" && $phpChoice === "pierre") ||
                ($playerChoice === "ciseaux" && $phpChoice === "feuille")
        ) {
            $result = "Gagné";

        } else {
            $result = "Perdu";
        }
    }
}



$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu Pierre, Feuilles, Ciseaux</title>

 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">

<div class="container py-5">

    <h1 class="text-center mb-4">Jeu Pierre, Feuilles, Ciseaux</h1>

    <div class="card p-4 shadow-sm">

        <h4>Votre choix :</h4>
        <p><strong>{$playerChoice}</strong></p>

        <h4>Choix de PHP :</h4>
        <p><strong>{$phpChoice}</strong></p>

        <h4>Résultat :</h4>
        <p><strong>{$result}</strong></p>

        <hr>

        <div class="d-flex gap-3">

            <a href="?player=pierre" class="btn btn-primary">Pierre</a>
            <a href="?player=feuille" class="btn btn-success">Feuille</a>
            <a href="?player=ciseaux" class="btn btn-danger">Ciseaux</a>

        </div>

        <hr>

        <a href="/" class="btn btn-secondary">Réinitialiser</a>

    </div>
</div>

</body>
</html>
HTML;


echo $html;

?>




