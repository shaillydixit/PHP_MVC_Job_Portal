<?php 

$config = require basePath('config/db.php');
$db = new Database($config);

$id = $_GET['id'] ?? '';

$params = [
    'id' => $id,
];

$listing = $db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

if (!$listing) {
    // Handle the case where the listing was not found
    echo "Listing not found.";
    exit; // Stop further script execution
}

loadView('listings/show', [
    'listing' => $listing
]);