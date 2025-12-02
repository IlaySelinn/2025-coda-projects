<?php
require_once 'inc/page.inc.php';

$message = $_GET['message'] ?? 'An error occurred';

$html = "<div class=\"page-container\">
    <div class=\"hero\">
        <h1>Error</h1>
        <p>" . htmlspecialchars($message) . "</p>
    </div>
    <div class=\"section-footer\">
        <a href=\"index.php\" class=\"button primary-button\">← Back to Home</a>
    </div>
</div>";

echo (new HTMLPage("Lowify - Error"))
    ->addHead('<meta name="viewport" content="width=device-width, initial-scale=1">')
    ->addStylesheet("inc/style.css")
    ->addContent($html)
    ->render();
?>
