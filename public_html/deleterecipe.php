<?php

require_once '../config.php';
require_once  LIBRARY_PATH . '/connectdb.php';

if (isset($_POST['id'])) {
  $mongodb_id = new \MongoDB\BSON\ObjectId($_POST['id']);
  $result = $collection->deleteOne([ '_id' => $mongodb_id]);
  if ($result->getDeletedCount() >= 1) {
    echo "True";
  } else {
    echo "False";
  }
}
