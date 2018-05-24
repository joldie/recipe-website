<?php

// Connect to MongoDB database
require 'vendor/autoload.php'; // Include Composer's autoloader
require_once '../resources/config.php';
$client = new MongoDB\Client("mongodb://{$config['db']['server']}:{$config['db']['port']}");

// Test if connection was successful
try {
    $dbs = $client->listDatabases();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Unable to connect to MongoDB. Please check connection settings.";
}

$collection = $client->{$config['db']['name']}->{$config['db']['collection']};

if (isset($_POST['name'])) {

  $result = $collection->findOne([ 'name' => $_POST['name']]);

  if ($result == null) {
    echo "False";
  }
  else {
    echo "True";
  }

}
