<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1 style='color:lime;font-size:60px;'>PHP OK ✅</h1>";

echo "<h3>inc/ dosyaları kontrolü:</h3>";
echo file_exists('../inc/database.inc.php') ? "✅ database.inc.php VAR<br>" : "❌ database.inc.php YOK<br>";
echo file_exists('../inc/page.inc.php') ? "✅ page.inc.php VAR<br>" : "❌ page.inc.php YOK<br>";

try {
    require_once '../inc/database.inc.php';
    $db = new DatabaseManager("mysql:host=mysql;dbname=lowify;charset=utf8mb4", "lowify", "lowifypassword");
    echo "<p style='color:lime;'>✅ DatabaseManager OK</p>";
    
    $count = $db->executeQuery("SELECT COUNT(*) as c FROM artist")[0]['c'];
    echo "<p>Sanatçı sayısı: <b>$count</b></p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database ERROR: " . $e->getMessage() . "</p>";
}

try {
    require_once '../inc/page.inc.php';
    $page = new HTMLPage("Test");
    echo "<p style='color:orange;'>✅ HTMLPage OK</p>";
    $render = $page->render();
    echo "<p>Render uzunluğu: " . strlen($render) . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ HTMLPage ERROR: " . $e->getMessage() . "</p>";
}
