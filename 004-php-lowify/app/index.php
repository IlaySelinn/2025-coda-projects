<?php
require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';




$host = "mysql";
$dbname = "lowify";
$username = "lowify";
$password = "lowifypassword";

$db = null;
$topArtistsHTML = "";
$recentAlbumsHTML = "";
$bestAlbumsHTML = "";
$searchOptionsHTML = "";

/**
 * Database connection
 */
try {
    $db = new DatabaseManager(
        dsn: "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        username: $username,
        password: $password
    );
} catch (PDOException $ex) {
    die("Database connection failed: " . $ex->getMessage());
}

/*Top 5 Most Popular Artists*/
try {
    $topArtists = $db->executeQuery("
        SELECT id, name, cover, monthly_listeners 
        FROM artist 
        ORDER BY monthly_listeners DESC 
        LIMIT 5
    ");

    foreach ($topArtists as $artist) {
        $name = htmlspecialchars($artist['name']);
        $cover = htmlspecialchars($artist['cover']);
        $id = (int)$artist['id'];
        $listeners = number_format($artist['monthly_listeners']);

        $topArtistsHTML .= "<div class=\"card-item artist\">
            <a href=\"artist.php?id=$id\" title=\"$name - Artist Details\">
                <img src=\"$cover\" alt=\"Artist photo: $name\" loading=\"lazy\">
                <h5>$name</h5>
                <p class=\"listeners\">$listeners listeners</p>
            </a>
        </div>";
    }
} catch (PDOException $ex) {
    error_log("Top artists query failed: " . $ex->getMessage());
}

/**Top 5 Most Recent Albums*/
try {
    $recentAlbums = $db->executeQuery("
        SELECT a.id as album_id, a.name as album_name, a.cover, 
               a.release_date, art.id as artist_id, art.name as artist_name
        FROM album a 
        INNER JOIN artist art ON a.artist_id = art.id 
        ORDER BY a.release_date DESC 
        LIMIT 5
    ");

    foreach ($recentAlbums as $album) {
        $albumName = htmlspecialchars($album['album_name']);
        $cover = htmlspecialchars($album['cover']);
        $artistName = htmlspecialchars($album['artist_name']);
        $albumId = (int)$album['album_id'];
        $artistId = (int)$album['artist_id'];
        $year = date('Y', strtotime($album['release_date']));

        $recentAlbumsHTML .= "<div class=\"card-item album\">
            <a href=\"album.php?id=$albumId\" title=\"$albumName - Album Details\">
                <img src=\"$cover\" alt=\"Album cover: $albumName\" loading=\"lazy\">
                <h5>$albumName</h5>
                <p><a href=\"artist.php?id=$artistId\" class=\"artist-link\">$artistName</a> ($year)</p>
            </a>
        </div>";
    }
} catch (PDOException $ex) {
    error_log("Recent albums query failed: " . $ex->getMessage());
}

/**Top 5 Best Rated Album*/
try {
    $bestAlbums = $db->executeQuery("
        SELECT a.id as album_id, a.name as album_name, a.cover,
               art.name as artist_name,
               ROUND(AVG(s.note), 1) as avg_rating
        FROM album a 
        INNER JOIN artist art ON a.artist_id = art.id
        INNER JOIN song s ON a.id = s.album_id
        GROUP BY a.id, a.name, a.cover, art.name
        ORDER BY avg_rating DESC 
        LIMIT 5
    ");

    foreach ($bestAlbums as $album) {
        $albumName = htmlspecialchars($album['album_name']);
        $cover = htmlspecialchars($album['cover']);
        $artistName = htmlspecialchars($album['artist_name']);
        $albumId = (int)$album['album_id'];
        $rating = $album['avg_rating'];

        $bestAlbumsHTML .= "<div class=\"card-item album rated\">
            <a href=\"album.php?id=$albumId\" title=\"$albumName - Album Details\">
                <img src=\"$cover\" alt=\"Album cover: $albumName\" loading=\"lazy\">
                <h5>$albumName</h5>
                <p class=\"rating\">★ $rating/5 by $artistName</p>
            </a>
        </div>";
    }
} catch (PDOException $ex) {
    error_log("Best albums query failed: " . $ex->getMessage());
}

/**
 * Search autocomplete
 */
try {
    $searchData = $db->executeQuery("
        SELECT DISTINCT name FROM artist 
        UNION 
        SELECT DISTINCT name FROM album
        UNION 
        SELECT DISTINCT name FROM song
        LIMIT 100
    ");

    foreach ($searchData as $item) {
        $searchOptionsHTML .= "<option value=\"" . htmlspecialchars($item['name']) . "\">";
    }
} catch (PDOException $ex) {
    error_log("Search data query failed: " . $ex->getMessage());
}

$html = "<div class=\"page-container\">
    <header class=\"hero\">
        <h1>🎵 Lowify</h1>
        <p>Discover your favorite music</p>
    </header>

    <div class=\"search-section\">
        <form action=\"search.php\" method=\"POST\" class=\"search-form\">
            <div class=\"search-input-group\">
                <input type=\"search\" id=\"site-search\" name=\"search\" 
                       list=\"suggestions\" placeholder=\"Search artists, albums, songs...\" 
                       autocomplete=\"off\">
                <datalist id=\"suggestions\">$searchOptionsHTML</datalist>
                <button type=\"submit\">🔍</button>
            </div>
        </form>
    </div>
    
    <section class=\"content-section\">
        <h2>Top Artists</h2>
        <div class=\"card-grid\">$topArtistsHTML</div>
        <div class=\"section-footer\">
            <a href=\"artists.php\" class=\"button primary-button\">View All Artists →</a>
        </div>
    </section>
    
    <section class=\"content-section\">
        <h2>New Releases</h2>
        <div class=\"card-grid\">$recentAlbumsHTML</div>
    </section>
    
    <section class=\"content-section\">
        <h2>Top Rated</h2>
        <div class=\"card-grid\">$bestAlbumsHTML</div>
    </section>
</div>";

echo (new HTMLPage("Lowify - Home"))
    ->addHead('<meta name="description" content="Discover top artists, new releases and best rated albums">')
    ->addHead('<meta name="viewport" content="width=device-width, initial-scale=1">')
    ->addStylesheet("inc/style.css")
    ->addContent($html)
    ->render();
?>
