<?php

require 'common_top.php';
require 'index_top.view.php';


// Connect to MongoDB database
require 'vendor/autoload.php'; // Include Composer's autoloader
require_once '../resources/config.php';
$client = new MongoDB\Client("mongodb://{$config['db']['server']}:{$config['db']['port']}");

// Test if connection was successful
try {
    $dbs = $client->listDatabases();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Unable to connect to MongoDB. Please check connection string.";
}

$collection = $client->{$config['db']['name']}->{$config['db']['collection']};

if (isset($_POST['search'])) {
  // Extract all words from search text (ignoring punctuation, special chars)
  $delimiters = array("\n", "\t", ",", ".", "!", "?", ":", ";", "'", '"');
  $search_string = str_replace($delimiters, " ", $_POST['search']);

  // Search DB collection for recipes based on keywords (limit to 12 results)
  $recipes = $collection->find(['$text'=> ['$search'=>$search_string]], ['limit' => 12]);
} else {
  // Retrieve maximum 12 receipes from DB
  $recipes = $collection->find([], ['limit' => 12]);
}

// For each recipe, display a "card" div with recipe name and image
$count_recipes = 0;
foreach ($recipes as $recipe) {
  $count_recipes += 1;
  $recipe_id = (string)$recipe['_id'];
  $recipe_name = $recipe['name'];
  $image_bin = base64_encode($recipe['image']->getData());
  $image_url = "data:image/" . $recipe['image_type'] . ";base64," . $image_bin;
  require 'card.view.php';
}

if ($count_recipes == 0) {
  echo "<h4>No recipes found<br /><br /> Try again or <a href='index.php' style='text-decoration:underline'>return home</a></h4>";
}

require 'index_bottom.view.php';
require 'common_bottom.php';
