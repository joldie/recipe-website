<?php

require 'common_top.php';
require 'index_top.view.php';


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

// Retrieve maximum 12 receipes from DB
$recipes = $client->$db_name->$db_collection->find([], ['limit' => 12]);

// For each recipe, display a "card" div with recipe name and image
foreach ($recipes as $recipe) {
  $recipe_id = (string)$recipe['_id'];
  $recipe_name = $recipe['name'];
  $image_bin = base64_encode($recipe['image']->getData());
  $image_url = "data:image/" . $recipe['image_type'] . ";base64," . $image_bin;
  require 'card.view.php';
}

require 'index_bottom.view.php';
require 'common_bottom.php';
