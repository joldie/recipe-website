<?php

require_once '../config.php';
require_once  LIBRARY_PATH . '/connectdb.php';

$input = json_decode(file_get_contents('php://input'));

if (isset($input->name)) {

  $result = $collection->findOne([ 'name' => $input->name ]);

  if ($result == null) {
    echo json_encode("False");
  }
  else {
    echo json_encode("True");
  }

}
