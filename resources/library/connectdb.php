<?php

require_once '../config.php';

// Connect to MongoDB database
require_once 'vendor/autoload.php'; // Include Composer's autoloader
$client = new MongoDB\Client("mongodb://{$config['db']['server']}:{$config['db']['port']}");

// Test if connection was successful
try {
    $dbs = $client->listDatabases();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Unable to connect to MongoDB. Please check connection settings.";
}

// Save recipes collection in variable
$collection = $client->{$config['db']['name']}->{$config['db']['collection']};
