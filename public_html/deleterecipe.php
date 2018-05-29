<?php

require_once '../config.php';
require_once  LIBRARY_PATH . '/connectdb.php';

$input = json_decode(file_get_contents('php://input'));

if (isset($input->id)) {

  $mongodb_id = new \MongoDB\BSON\ObjectId($input->id);
  $result = $collection->deleteOne([ '_id' => $mongodb_id]);

  if ($result->getDeletedCount() >= 1) {
    echo json_encode("True");
  } else {
    echo json_encode("False");
  }

}
