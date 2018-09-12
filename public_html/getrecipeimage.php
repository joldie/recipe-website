<?php

require_once '../config.php';
require_once  LIBRARY_PATH . '/connectdb.php';

$input = json_decode(file_get_contents('php://input'));

if (isset($input->id)) {

  $mongodb_id = new \MongoDB\BSON\ObjectId($input->id);
  $recipes = $collection->find(['_id' => $mongodb_id]);

  if ($recipes == null) {
    echo json_encode("False");
  } else {
    foreach ($recipes as $recipe) {
      $image_bin = base64_encode($recipe['image']->getData());
      $image_url = "data:image/" . $recipe['image_type'] . ";base64," . $image_bin;
      echo json_encode($image_url);
      break;
    }
    
  }

}
