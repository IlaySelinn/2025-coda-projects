<?php

// -- L'importation des librairies à l'aide de require_once
require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

// -- L'initialisation de la connexion à la base de données
try
{
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    header('Location: error.php?message=Erreur DB connexion');
    exit;
}

// -- Je récupère les infos de tous les artistes depuis la base de données
try {
    $allArtists = $db->executeQuery(<<<SQL
        SELECT 
            id,
            name, 
            cover
        FROM artist
        ORDER BY name ASC
SQL);
} catch (PDOException $ex) {
    header('Location: error.php?message=Erreur requête artistes');
    exit;
}

// -- Je  crée une variable pour contenir le HTML qui représente la liste des artistes
$artistsAsHTML = "";
$iterator = 0;

// -- Pour chaque artiste récupéré depuis la base de donnée
foreach ($allArtists as $artist)
{
    $artistName = htmlspecialchars($artist['name']);
    $artistId = (int)$artist['id'];
    $artistCover = htmlspecialchars($artist['cover']);

    if ($iterator % 4 == 0) {
        $artistsAsHTML .= '<div class="row mb-4">';
    }

    // -- on ajoute une carte HTML représentant l'artiste courant
    $artistsAsHTML .= <<<HTML
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="artist.php?id={$artistId}" class="text-decoration-none text-white">
                <div class="card h-100 bg-dark text-white border-dark shadow">
                    <img src="{$artistCover}" class="card-img-top rounded-circle" alt="{$artistName}">
                    <div class="card-body bg-secondary-subtle text-white">
                        <h5 class="card-title">{$artistName}</h5>
                    </div>
                </div>
            </a>
        </div>
HTML;

    if ($iterator % 4 == 3)
    {
        $artistsAsHTML .= '</div>';
    }
    $iterator++;
}

if ($iterator % 4 != 0) {
    $artistsAsHTML .= '</div>';
}

// -- on crée la structure HTML de notre page
$html = <<<HTML
<div class="container bg-dark text-white p-4">
    <a href="index.php" class="link text-white mb-4 d-block"> < Retour à l'accueil</a>
    <h1 class="mb-4">Artistes</h1>    
    <div>{$artistsAsHTML}</div>
</div>
HTML;

// -- on génère et on affiche la page
echo (new HTMLPage(title: "Artistes - Lowify"))
    ->setupBootstrap([
    ])
    ->setupNavigationTransition()
    ->addContent($html)
    ->render();
?>
