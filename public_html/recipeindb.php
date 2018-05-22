<?php

// Connect to MongoDB database
require 'vendor/autoload.php'; // Include Composer's autoloader
require_once '../db_login.php';
$client = new MongoDB\Client("mongodb://{$db_server}:{$db_port}");

// Test if connection was successful
try {
    $dbs = $client->listDatabases();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Unable to connect to MongoDB. Please check connection settings.";
}

$collection = $client->$db_name->$db_collection;

if (isset($_POST['name'])) {

  $result = $collection->findOne([ 'name' => $_POST['name']]);

  if ($result == null) {
    echo "False";
  }
  else {
    echo "True";
  }

}
