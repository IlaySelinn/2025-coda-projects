<?php
$choices= ['Pierre', 'Feuille', 'Ciseaux'];

$playerChoice = '';
$phpChoice = '';
$result = '';

function calculerResultat($player, $pc)
{
if ($player === $pc)
{
    return 'Égalité';
}

    if (
            ($player === 'Pierre' && $pc === 'Ciseaux') ||
            ($player === 'Feuille' && $pc === 'Pierre') ||
            ($player === 'Ciseaux' && $pc === 'Feuille')
    ) {
        return 'Gagné !';
    } else
    {
        return 'Perdu !';
    }
}

if (isset($_GET['player'])) {
    $playerChoice = $_GET['player'];



    if (!in_array($playerChoice, $choices, true)) {
        $playerChoice = '';
        $result = 'Choix invalide.';
    } else {

        $phpChoice = $choices[array_rand($choices)];


        $result = calculerResultat($playerChoice, $phpChoice);
    }
}
?>


<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Pierre, Feuille, Ciseaux</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background:bisque; }
        .container { max-width:600px; margin:0 auto; background:rosybrown; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.08); }
        h1 { text-align:center; margin-bottom:20px; }
        .status { display:flex; justify-content:space-between; gap:10px; margin-bottom:16px; }
        .box { flex:1; padding:12px; border:1px solid #ddd; border-radius:6px; text-align:center; background:#fafafa; }
        .result { text-align:center; font-weight:700; padding:12px; margin-bottom:12px; border-radius:6px; }
        .result.egal { background:#ffe680; }
        .result.gagne { background:#d4f7d4; }
        .result.perdu { background:#ffd6d6; }
        .buttons { display:flex; gap:8px; justify-content:center; margin-bottom:12px; }
        .buttons form { display:inline-block; margin:0; }
        button { padding:10px 14px; border-radius:6px; border:1px solid #ccc; background:#fff; cursor:pointer; font-weight:600; }
        .reset { display:block; text-align:center; margin-top:10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Pierre, Feuille, Ciseaux</h1>


    <div class="status">
        <div class="box">
            <strong>Votre choix</strong>
            <div id="playerChoice" style="margin-top:8px;">
                <?= $playerChoice !== '' ? htmlspecialchars($playerChoice) : '—' ?>
            </div>
        </div>

        <div class="box">
            <strong>Choix de PHP</strong>
            <div id="phpChoice" style="margin-top:8px;">
                <?= $phpChoice !== '' ? htmlspecialchars($phpChoice) : '—' ?>
            </div>
        </div>
    </div>


    <?php if ($result !== ''): ?>
        <?php
        $cls = '';
        if ($result === 'Égalité') $clear= 'egal';
        elseif ($result === 'Gagné !') $clear = 'gagne';
        elseif ($result === 'Perdu !') $clear = 'perdu';
        ?>
        <div class="result <?= $clear ?>">
            <?= htmlspecialchars($result) ?>
        </div>
    <?php else: ?>
        <div class="result" style="background:#eef6ff;">Choisissez une option pour jouer.</div>
    <?php endif; ?>


    <div class="buttons">
        <form method="post" style="display:inline;">
            <input type="hidden" name="choice" value="Pierre">
            <button type="submit">Pierre</button>
        </form>

        <form method="post" style="display:inline;">
            <input type="hidden" name="choice" value="Feuille">
            <button type="submit">Feuille</button>
        </form>

        <form method="post" style="display:inline;">
            <input type="hidden" name="choice" value="Ciseaux">
            <button type="submit">Ciseaux</button>
        </form>
    </div>



    <div class="reset">
        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">Réinitialiser le jeu</a>
    </div>
</div>
</body>
</html>



