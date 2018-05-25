<?php

require_once '../config.php';
require_once  LIBRARY_PATH . '/connectdb.php';

if (isset($_POST['name'])) {

  $result = $collection->findOne([ 'name' => $_POST['name']]);

  if ($result == null) {
    echo "False";
  }
  else {
    echo "True";
  }

}
