<?php
// GENERATUER DE MOT DE PASSE-PROJET

// Configuration du mot de passe
$longueurMin = 8;
$longueurMax = 42;

// Récupération des données du formulaire
$taille = isset($_POST['taille']) ? (int)$_POST['taille'] : 12;
$utiliserMin = $_POST['use-min'] ?? '1';
$utiliserMaj = $_POST['use-maj'] ?? '1';
$utiliserChiffres = $_POST['use-chiffres'] ?? '1';
$utiliserSymboles = $_POST['use-symboles'] ?? '1';

// Validation de la longueur
$taille = max($longueurMin, min($longueurMax, $taille));

// Les fonctions
function garderCoche(string $valeur): string
{
    return $valeur === '1' ? 'checked' : '';
}

function optionsLongueur(int $actuelle, int $min, int $max): string
{
    $options = '';
    for ($i = $min; $i <= $max; $i++)
    {
        $selected = $i === $actuelle ? 'selected' : '';
        $options .= "<option value=\"$i\" $selected>$i</option>";
    }
    return $options;
}

function caractereAleatoire(string $chars): string
{
    return $chars[random_int(0, strlen($chars) - 1)];
}

function genererMotDePasse(int $taille, bool $min, bool $maj, bool $chiffres, bool $symboles): string
{
    $ensembles = [];

    if ($min) $ensembles[] = 'abcdefghijklmnopqrstuvwxyz';
    if ($maj) $ensembles[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($chiffres) $ensembles[] = '0123456789';
    if ($symboles) $ensembles[] = '!@#$%^&*';

    if (empty($ensembles)) {
        return 'Choisissez au moins un type de caractères.';
    }

    $mdp = '';
    // Au moins un de chaque type sélectionné
    foreach ($ensembles as $ensemble)
    {
        $mdp .= caractereAleatoire($ensemble);
    }

    // Compléter le reste
    $reste = $taille - strlen($mdp);
    for ($i = 0; $i < $reste; $i++)
    {
        $ensemble = $ensembles[random_int(0, count($ensembles) - 1)];
        $mdp .= caractereAleatoire($ensemble);
    }

    return str_shuffle($mdp);
}

// Génération du mot de passe
$optionsLongueur = optionsLongueur($taille, $longueurMin, $longueurMax);
$motDePasseGenere = genererMotDePasse(
    $taille,
    $utiliserMin === '1',
    $utiliserMaj === '1',
    $utiliserChiffres === '1',
    $utiliserSymboles === '1'
);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Générateur de Mot de Passe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <section class="result">
            <h2>Mot de passe généré:</h2>
            de class="password-display"><?= htmlspecialchars($motDePasseGenere) ?></code>
        </section>
    <?php endif; ?>

    <form method="POST" class="form">
        <div class="field">
            <label for="taille">Longueur:</label>
            <select id="taille" name="taille">
                <?= $optionsLongueur ?>
            </select>
        </div>

        <div class="checkbox-group">
            <div class="checkbox">
                <input type="checkbox" id="use-min" name="use-min" value="1" <?= garderCoche($utiliserMin) ?>>
                <label for="use-min">Minuscules (a-z)</label>
            </div>

            <div class="checkbox">
                <input type="checkbox" id="use-maj" name="use-maj" value="1" <?= garderCoche($utiliserMaj) ?>>
                <label for="use-maj">Majuscules (A-Z)</label>
            </div>

            <div class="checkbox">
                <input type="checkbox" id="use-chiffres" name="use-chiffres" value="1" <?= garderCoche($utiliserChiffres) ?>>
                <label for="use-chiffres">Chiffres (0-9)</label>
            </div>

            <div class="checkbox">
                <input type="checkbox" id="use-symboles" name="use-symboles" value="1" <?= garderCoche($utiliserSymboles) ?>>
                <label for="use-symboles">Symboles</label>
            </div>
        </div>

        <button type="submit" class="generate-btn">Générer</button>
    </form>

    <style>

    </style>

</main>
</body>
</html>




