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
    <title>Générateur de Mot de Passe Sécurisé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex align-items-center py-4">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white text-center py-4">
                    <h1 class="display-5 fw-bold mb-2">🔐 Générateur</h1>
                    <h2 class="fs-4 fw-normal opacity-90 mb-0">Mot de Passe Sécurisé</h2>
                </div>

                <div class="card-body p-5">
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-3 fs-1 text-success"></i>
                                <div>
                                    <h4 class="alert-heading mb-2">Mot de passe généré !</h4>
                                    <div class="bg-dark text-white p-3 rounded mb-2 lh-1" style="font-family: monospace; font-size: 1.5rem; letter-spacing: 2px; word-break: break-all;">
                                        <?= htmlspecialchars($motDePasseGenere) ?>
                                    </div>
                                    <small class="text-muted">Copiez-le et utilisez-le immédiatement</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- Longueur -->
                        <div class="mb-4">
                            <label for="taille" class="form-label h5 fw-bold text-dark mb-3">📏 Longueur</label>
                            <select class="form-select form-select-lg" id="taille" name="taille">
                                <?= $optionsLongueur ?>
                            </select>
                        </div>

                        <!-- Options de caractères -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="use-min" name="use-min" value="1" <?= garderCoche($utiliserMin) ?>>
                                    <label class="form-check-label fs-5 fw-semibold" for="use-min">
                                        <span class="badge bg-success me-2">a-z</span>
                                        Lettres minuscules
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="use-maj" name="use-maj" value="1" <?= garderCoche($utiliserMaj) ?>>
                                    <label class="form-check-label fs-5 fw-semibold" for="use-maj">
                                        <span class="badge bg-info me-2">A-Z</span>
                                        Lettres majuscules
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="use-chiffres" name="use-chiffres" value="1" <?= garderCoche($utiliserChiffres) ?>>
                                    <label class="form-check-label fs-5 fw-semibold" for="use-chiffres">
                                        <span class="badge bg-warning text-dark me-2">0-9</span>
                                        Chiffres
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="use-symboles" name="use-symboles" value="1" <?= garderCoche($utiliserSymboles) ?>>
                                    <label class="form-check-label fs-5 fw-semibold" for="use-symboles">
                                        <span class="badge bg-danger me-2">!@#</span>
                                        Symboles spéciaux
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-4 fs-4 fw-bold shadow-lg border-0" style="background: linear-gradient(45deg, #0d6efd, #6610f2);">
                            <i class="bi bi-lightning-charge-fill me-3 fs-3"></i>
                            GÉNÉRER MAINTENANT
                        </button>
                    </form>

                    <!-- Infos techniques -->
                    <div class="mt-5 pt-4 border-top">
                        <small class="text-muted">
                            <strong>✅ Sécurité:</strong> random_int() cryptographique,
                            garantie 1 caractère par type, mélange automatique
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



