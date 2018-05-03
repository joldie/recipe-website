<?php

require 'common_top.php';

// Connect to MongoDB database
require 'vendor/autoload.php'; // Include Composer's autoloader
require_once '../db_login.php';
$client = new MongoDB\Client("mongodb://{$db_server}:{$db_port}");

// Test if connection was successful
try {
    $dbs = $client->listDatabases();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Unable to connect to MongoDB. Please check connection string.";
}

// Get recipe ID from URL, sanitise input and create MongoDB ID object
try {
    // Exception will be thrown if ID in URL not in expected format
    $id = new \MongoDB\BSON\ObjectId(htmlspecialchars($_GET['id']));
} catch (Exception $e) {
    // Continue execution, regardless
}

// Check if recipe already in DB
$collection = $client->$db_name->$db_collection;
$result = $collection->findOne([ '_id' => $id]);

// Only update HTML if recipe in DB, otherwise display default page
if ($result !== null) {
    $dom = new DOMDocument();
    // HTML template for displaying recipe
    $template_html = file_get_contents("recipe.view.php");
    // Options prevent addition of doctype, <html> and <body> tags
    $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $dom->getElementById('recipe-name')->nodeValue = $result['name'];
    $dom->getElementById('recipe-description')->nodeValue = $result['description'];
    $dom->getElementById('serves')->nodeValue = $result['serves'];
    $dom->getElementById('preptime')->nodeValue = $result['preptime'];
    $dom->getElementById('cooktime')->nodeValue = $result['cooktime'];
    if ($result['credit'] == null) {
      // TODO: Hide credit text
      //$dom->getElementById('credit')->...
    } elseif ($result['credit_link'] == null) {
      $dom->getElementById('credit')->nodeValue = $result['cooktime'];
    } else {
      // TODO: Add link
      //$dom->getElementById('credit')->nodeValue = "<a href='" . $result['credit_link'] . "'>" . $result['credit'] . "</a>";
    }

    foreach ($result['ingredients'] as $ingredient) {
        $new_item = $dom->createElement('li', $ingredient['qty'] . ' ' .
            $ingredient['unit'] . ' ' . $ingredient['item']);
        $dom->getElementById('ingredients-list')->appendChild($new_item);
    }

    foreach ($result['steps'] as $step) {
        $new_item = $dom->createElement('li', $step);
        $dom->getElementById('steps-list')->appendChild($new_item);
    }

    $image_bin = base64_encode($result['image']->getData());
    $dom->getElementById('recipe-image')->setAttribute("src", "data:image/" . $result['image_type'] . ";base64, $image_bin ");


    echo $dom->saveHTML();
} else {
    // Display "recipe not found" page
    require 'recipe_not_found.view.php';
}

require 'common_bottom.php';
