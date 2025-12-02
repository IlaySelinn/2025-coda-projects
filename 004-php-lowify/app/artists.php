<?php
require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

$host = "mysql";
$dbname = "lowify";
$username = "lowify";
$password = "lowifypassword";

try {
    $db = new DatabaseManager(
        dsn: "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        username: $username,
        password: $password
    );
} catch (PDOException $ex) {
    header("Location: error.php?message=" . urlencode("Database connection failed"));
    exit;
}

try {
    $artists = $db->executeQuery("
        SELECT id, name, cover, monthly_listeners 
        FROM artist 
        ORDER BY name ASC
    ");
    $artistCount = count($artists);
} catch (PDOException $ex) {
    header("Location: error.php?message=" . urlencode("Error fetching artists"));
    exit;
}

$artistsHTML = "";
foreach ($artists as $artist) {
    $id = (int)$artist['id'];
    $name = htmlspecialchars($artist['name']);
    $cover = htmlspecialchars($artist['cover']);
    $listeners = number_format($artist['monthly_listeners']);

    $artistsHTML .= <<<HTML
<div class="card-item artist">
    <a href="artist.php?id=$id" title="$name - Artist Details">
        <img src="$cover" alt="Artist photo: $name" loading="lazy">
        <h5>$name</h5>
        <p class="listeners">$listeners listeners</p>
    </a>
</div>
HTML;
}

$html = <<<HTML
<div class="page-container">
    <header class="hero">
        <h1>All Artists</h1>
        <p>SEARCH YOUR ARTIST!($artistCount artists)</p>
    </header>

    <section class="content-section">
        <h2>Artists</h2>
        <div class="card-grid">
            $artistsHTML
        </div>
    </section>

    <div class="section-footer">
        <a href="index.php" class="button primary-button">← Back to Home</a>
    </div>
</div>
HTML;

echo (new HTMLPage("Lowify - Artists"))
    ->addHead('<meta name="description" content="Browse all artists on Lowify">')
    ->addHead('<meta name="viewport" content="width=device-width, initial-scale=1">')
    ->addStylesheet("inc/style.css")
    ->addContent($html)
    ->render();
?>
