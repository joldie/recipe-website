<?php

require 'common_top.php';
require 'addrecipe.view.php';

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

$collection = $client->$db_name->$db_collection;

// Dummy recipe data for testing...
$name = "Vegan Pizza";
$date = date("Y-m-d H:i:s", time());
$description = "Recipe description";
$credit = "Vegan Richa";
$credit_link = "https://www.veganricha.com/2016/08/easy-veggie-vegan-pizza.html";
$tags = ['dinner', 'pasta'];
$serves = 1;
$preptime = 15;
$cooktime = 30;
$ingredient1 = [ 'qty' => 1, 'unit' => 'tsp.', 'item' => 'Baking soda' ];
$ingredient2 = [ 'qty' => 1, 'unit' => '', 'item' => 'Apple' ];
$ingredient3 = [ 'qty' => 20, 'unit' => 'mL', 'item' => 'Oil' ];
$ingredient4 = [ 'qty' => 1.5, 'unit' => 'tbsp.', 'item' => 'Sugar' ];
$ingredients = [$ingredient1, $ingredient2, $ingredient3, $ingredient4];
$step1 = "Dice all ingredients.";
$step2 = "Mix all ingredients.";
$step3 = "Cook.";
$step4 = "Eat!";
$steps = [$step1, $step2, $step3, $step4];
$image = new MongoDB\BSON\Binary(file_get_contents("images/vegan-pizza.jpg"), MongoDB\BSON\Binary::TYPE_GENERIC);
$image_type = "jpg";

// Only insert recipe if not already in DB
$result = $collection->findOne([ 'name' => $name]);
if ($result == null) {
	$result = $collection->insertOne([
    'name' => $name,
    'date' => $date,
    'credit' => $credit,
    'credit_link' => $credit_link,
    'description' => $description,
    'tags' => $tags,
    'serves' => $serves,
    'preptime' => $preptime,
    'cooktime' => $cooktime,
    'ingredients' => $ingredients,
    'steps' => $steps,
		'image' => $image,
		'image_type' => $image_type
	]);
	echo "Recipe saved in database (ID: {$result->getInsertedId()})";
} else {
	echo "Recipe with name '{$name}' already exists in database.";
}

require 'common_bottom.php';
