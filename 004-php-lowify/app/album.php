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

$albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($albumId <= 0) {
    header("Location: error.php?message=" . urlencode("Invalid album ID"));
    exit;
}

try {
    $album = $db->executeQuery("
        SELECT a.id, a.name, a.cover, a.release_date, 
               art.id as artist_id, art.name as artist_name
        FROM album a 
        INNER JOIN artist art ON a.artist_id = art.id 
        WHERE a.id = ?
    ", [$albumId])[0] ?? null;
} catch (PDOException $ex) {
    header("Location: error.php?message=" . urlencode("Error fetching album"));
    exit;
}

if (!$album) {
    header("Location: error.php?message=" . urlencode("Album not found"));
    exit;
}

try {
    $songs = $db->executeQuery("
        SELECT id, name, duration, note 
        FROM song 
        WHERE album_id = ? 
        ORDER BY id ASC
    ", [$albumId]);
} catch (PDOException $ex) {
    $songs = [];
}

function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf("%d:%02d", $minutes, $secs);
}

$albumName = htmlspecialchars($album['name']);
$artistName = htmlspecialchars($album['artist_name']);
$artistId = (int)$album['artist_id'];
$albumCover = htmlspecialchars($album['cover']);
$releaseDate = date('Y-m-d', strtotime($album['release_date']));
$songCount = count($songs);

$songsHTML = "";
foreach ($songs as $index => $song) {
    $songName = htmlspecialchars($song['name']);
    $duration = formatDuration($song['duration']);
    $note = round($song['note'], 1);

    $songsHTML .= <<<HTML
<div class="track-item">
    <span class="track-number">{$index}</span>
    <div class="track-info">
        <div class="track-title">$songName</div>
    </div>
    <div class="track-rating">★ $note</div>
    <div class="track-duration">$duration</div>
    <a href="like_song.php?id={$song['id']}" class="track-action" title="Like">❤️</a>
</div>
HTML;
}

$html = <<<HTML
<div class="page-container">
    <div class="album-hero">
        <div class="album-cover-section">
            <img src="$albumCover" alt="$albumName" class="album-cover-large">
        </div>
        <div class="album-info">
            <h1>$albumName</h1>
            <p><a href="artist.php?id=$artistId" class="artist-link">$artistName</a></p>
            <p class="release-date">Released: $releaseDate</p>
            <a href="index.php" class="button primary-button">← Back to Home</a>
        </div>
    </div>

    <section class="content-section">
        <h2>🎵 Tracks ($songCount songs)</h2>
        <div class="tracks-container">
            $songsHTML
        </div>
    </section>
</div>


HTML;

echo (new HTMLPage("Lowify - $albumName"))
    ->addHead('<meta name="description" content="Album: $albumName by $artistName">')
    ->addHead('<meta name="viewport" content="width=device-width, initial-scale=1">')
    ->addStylesheet("inc/style.css")
    ->addContent($html)
    ->render();
?>
