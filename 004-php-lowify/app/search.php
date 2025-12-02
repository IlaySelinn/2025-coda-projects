<?php
require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

//database
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

$query = trim($_GET['query'] ?? '');

if ($query === '') {
    // Boş istek olursa ana sayfaya yönlendir
    header("Location: index.php");
    exit;
}

try {
  //Chercher l'artist
    $searchArtists = $db->executeQuery("
        SELECT id, name, cover 
        FROM artist 
        WHERE name LIKE ?
        LIMIT 8
    ", ["%$query%"]);

    // Chercer l'album
    $searchAlbums = $db->executeQuery("
        SELECT a.id, a.name, a.cover, art.name AS artist_name
        FROM album a
        INNER JOIN artist art ON a.artist_id = art.id
        WHERE a.name LIKE ? OR art.name LIKE ?
        LIMIT 8
    ", ["%$query%", "%$query%"]);

    // Chercher la chanson
    $searchSongs = $db->executeQuery("
        SELECT s.id, s.name, s.duration, s.note, a.name AS album_name
        FROM song s
        INNER JOIN album a ON s.album_id = a.id
        WHERE s.name LIKE ?
        LIMIT 8
    ", ["%$query%"]);
} catch (PDOException $ex) {
    header("Location: error.php?message=" . urlencode("Search error"));
    exit;
}

function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf("%d:%02d", $minutes, $secs);
}

$artistsHTML = "";
foreach ($searchArtists as $artist) {
    $id = (int)$artist['id'];
    $name = htmlspecialchars($artist['name']);
    $cover = htmlspecialchars($artist['cover']);
    $artistsHTML .= <<<HTML
<div class="card-item">
    <a href="artist.php?id=$id" title="Artist: $name">
        <img src="$cover" alt="$name">
        <h5>$name</h5>
    </a>
</div>
HTML;
}

$albumsHTML = "";
foreach ($searchAlbums as $album) {
    $id = (int)$album['id'];
    $name = htmlspecialchars($album['name']);
    $artistName = htmlspecialchars($album['artist_name']);
    $cover = htmlspecialchars($album['cover']);
    $albumsHTML .= <<<HTML
<div class="card-item">
    <a href="album.php?id=$id" title="Album: $name">
        <img src="$cover" alt="$name">
        <h5>$name</h5>
        <p>$artistName</p>
    </a>
</div>
HTML;
}

$songsHTML = "";
foreach ($searchSongs as $song) {
    $id = (int)$song['id'];
    $name = htmlspecialchars($song['name']);
    $albumName = htmlspecialchars($song['album_name']);
    $duration = formatDuration($song['duration']);
    $note = round($song['note'], 1);
    $songsHTML .= <<<HTML
<div class="track-item">
    <span class="track-number">#</span>
    <div class="track-info">
        <div class="track-title">$name</div>
        <div class="track-subtitle">$albumName</div>
    </div>
    <div class="track-rating">★ $note</div>
    <div class="track-duration">$duration</div>
</div>
HTML;
}

$html = <<<HTML
<div class="page-container">
    <header class="hero">
        <h1>Search Results</h1>
        <p>Results for <strong>"$query"</strong></p>
    </header>

    <section class="content-section">
        <h2>Artists</h2>
        <div class="card-grid">
            $artistsHTML
        </div>
    </section>

    <section class="content-section">
        <h2>Albums</h2>
        <div class="card-grid">
            $albumsHTML
        </div>
    </section>

    <section class="content-section">
        <h2>Songs</h2>
        <div class="tracks-container">
            $songsHTML
        </div>
    </section>

    <div class="section-footer">
        <a href="index.php" class="button primary-button">← Back to Home</a>
    </div>
</div>
HTML;

echo (new HTMLPage("Lowify - Search: $query"))
    ->addHead('<meta name="viewport" content="width=device-width, initial-scale=1">')
    ->addStylesheet("inc/style.css")
    ->addContent($html)
    ->render();
?>

