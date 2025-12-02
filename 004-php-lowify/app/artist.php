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

// Information avec GET id
$artistId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($artistId <= 0) {
    header("Location: error.php?message=" . urlencode("Invalid artist ID"));
    exit;
}

/**
 * Sanatçı bilgilerini al
 */
try {
    $artist = $db->executeQuery("
        SELECT id, name, biography, cover, monthly_listeners 
        FROM artist 
        WHERE id = ?
    ", [$artistId])[0] ?? null;
} catch (PDOException $ex) {
    header("Location: error.php?message=" . urlencode("Error fetching artist"));
    exit;
}

if (!$artist) {
    header("Location: error.php?message=" . urlencode("Artist not found"));
    exit;
}

/**
 * Top 5 şarkı (en iyi notlar)
 */
try {
    $topSongs = $db->executeQuery("
        SELECT s.id, s.name, s.duration, s.note, a.name as album_name, a.cover
        FROM song s
        INNER JOIN album a ON s.album_id = a.id
        WHERE s.artist_id = ?
        ORDER BY s.note DESC 
        LIMIT 5
    ", [$artistId]);
} catch (PDOException $ex) {
    $topSongs = [];
}

/**
 * Tüm albümler
 */
try {
    $albums = $db->executeQuery("
        SELECT id, name, cover, release_date
        FROM album
        WHERE artist_id = ?
        ORDER BY release_date DESC
    ", [$artistId]);
} catch (PDOException $ex) {
    $albums = [];
}


function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf("%d:%02d", $minutes, $secs);
}

function formatListeners($number) {
    if ($number >= 1000000) return number_format($number/1000000, 1) . 'M';
    if ($number >= 1000) return number_format($number/1000, 1) . 'K';
    return number_format($number);
}


$artistName = htmlspecialchars($artist['name']);
$artistCover = htmlspecialchars($artist['cover']);
$biography = nl2br(htmlspecialchars($artist['biography'] ?? ''));
$listeners = formatListeners($artist['monthly_listeners']);

// Top Songs HTML
$topSongsHTML = "";
foreach ($topSongs as $song) {
    $songName = htmlspecialchars($song['name']);
    $albumName = htmlspecialchars($song['album_name']);
    $albumCover = htmlspecialchars($song['cover']);
    $duration = formatDuration($song['duration']);
    $note = round($song['note'], 1);

    $topSongsHTML .= "<div class=\"track-item\">
        <span class=\"track-number\">" . ($song['id'] % 100) . "</span>
        <img src=\"$albumCover\" alt=\"$songName\" class=\"track-cover\">
        <div>
            <div class=\"track-title\">$songName</div>
            <div class=\"track-subtitle\">$albumName</div>
        </div>
        <div class=\"track-rating\"> $note</div>
        <div class=\"track-duration\">$duration</div>
    </div>";
}

$albumsHTML = "";
foreach ($albums as $album) {
    $albumName = htmlspecialchars($album['name']);
    $albumCover = htmlspecialchars($album['cover']);
    $albumId = (int)$album['id'];
    $year = date('Y', strtotime($album['release_date']));

    $albumsHTML .= "<div class=\"card-item\">
        <a href=\"album.php?id=$albumId\">
            <img src=\"$albumCover\" alt=\"$albumName\">
            <h5>$albumName</h5>
            <p>($year)</p>
        </a>
    </div>";
}

$html = "<div class=\"page-container\">
    <div class=\"artist-hero\">
        <img src=\"$artistCover\" alt=\"$artistName\" class=\"artist-cover-large\">
        <div>
            <h1>$artistName</h1>
            <p class=\"listeners-large\">$listeners listeners</p>
            <div class=\"artist-bio\">$biography</div>
            <a href=\"index.php\" class=\"button primary-button\">← Back to Home</a>
        </div>
    </div>

    <section class=\"content-section\">
        <h2>Top Tracks</h2>
        <div class=\"tracks-container\">$topSongsHTML</div>
    </section>

    <section class=\"content-section\">
        <h2>Albums</h2>
        <div class=\"card-grid\">$albumsHTML</div>
    </section>
</div>";



echo (new HTMLPage("Lowify - $artistName"))
    ->addHead('<meta name="viewport" content="width=device-width, initial-scale=1">')
    ->addStylesheet("inc/style.css")
    ->addContent($html)
    ->render();
?>

