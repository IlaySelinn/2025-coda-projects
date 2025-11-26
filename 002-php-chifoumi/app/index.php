<?php


$choices = ["pierre", "feuille", "ciseaux"];

$playerChoice = $_GET['player'] ?? '';
$phpChoice = '';
$result = '';


if ($playerChoice !== '')
{


    if (!in_array($playerChoice, $choices, true))
    {

        $result = "Choix invalide.";

    }
    else
    {


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
    <title>Pierre, Feuilles, Ciseaux</title>

 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div>

    <h1 class="title">Pierre, Feuilles, Ciseaux</h1>
    <hr>

    
    <div>
        <h4>Votre choix :</h4>
        <p><strong>{$playerChoice}</strong></p>

        <h4>Choix de PHP :</h4>
        <p><strong>{$phpChoice}</strong></p>

        <h4>Résultat :</h4>
        <p><strong>{$result}</strong></p>

        <hr>

        <div>
            <a href="?player=pierre" class="btn btn-primary">Pierre</a>
            <a href="?player=feuille" class="btn btn-success">Feuille</a>
            <a href="?player=ciseaux" class="btn btn-danger">Ciseaux</a>
        </div>

        <hr>

        <a href="/" class="btn btn-secondary">Réinitialiser</a>
    </div>
</div>

<style>
body
{
background-color: beige;

}
.title
{
margin-top: 30px;
}
</style>

</body>
</html>
HTML;


echo $html;

?>




