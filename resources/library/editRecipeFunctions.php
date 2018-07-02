<?php

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| Helper functions for adding/editing recipes in HTML
|--------------------------------------------------------------------------
*/

function generate_ingredients_html($result) {

  $dom = new DOMDocument();
  // Load HTML template
  $template_html = file_get_contents(TEMPLATES_PATH . '/editrecipe.ingredients.view.php');
  // Options prevent addition of doctype, <html> and <body> tags
  $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  if (isset($result)) {
    $count = 1;
    foreach ($result['ingredients'] as $ingredient) {
      if ($count == 1) {
        $dom->getElementById('qty1')->setAttribute("value", $ingredient['qty']);
        $dom->getElementById('unit1')->setAttribute("value", $ingredient['unit']);
        $dom->getElementById('item1')->setAttribute("value", $ingredient['item']);
      } else {
        $new_item = $dom->getElementById('ingredient1')->cloneNode(true);
        $new_item->setAttribute("id", 'ingredient' . $count);
        $new_item->getElementsByTagName('*')->item(0)->setAttribute("id", 'qty' . $count);
        $new_item->getElementsByTagName('*')->item(0)->setAttribute("name", 'qty' . $count);
        $new_item->getElementsByTagName('*')->item(0)->setAttribute("value", $ingredient['qty']);
        $new_item->getElementsByTagName('*')->item(1)->setAttribute("id", 'unit' . $count);
        $new_item->getElementsByTagName('*')->item(1)->setAttribute("name", 'unit' . $count);
        $new_item->getElementsByTagName('*')->item(1)->setAttribute("value", $ingredient['unit']);
        $new_item->getElementsByTagName('*')->item(2)->setAttribute("id", 'item' . $count);
        $new_item->getElementsByTagName('*')->item(2)->setAttribute("name", 'item' . $count);
        $new_item->getElementsByTagName('*')->item(2)->setAttribute("value", $ingredient['item']);
        $dom->getElementById('ingredients')->appendChild($new_item);
      }
      $count += 1;
    }
  }

  return $dom->saveHTML();
}

function generate_steps_html($result) {

  $dom = new DOMDocument();
  // Load HTML template
  $template_html = file_get_contents(TEMPLATES_PATH . '/editrecipe.steps.view.php');
  // Options prevent addition of doctype, <html> and <body> tags
  $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  if (isset($result)) {
    $count = 1;
    foreach ($result['steps'] as $step) {
      if ($count == 1) {
        $dom->getElementById('step1')->nodeValue = $step;
      } else {
        $new_item = $dom->getElementById('step1')->cloneNode();
        $new_item->setAttribute("id", 'step' . $count);
        $new_item->setAttribute("name", 'step' . $count);
        $new_item->setAttribute("placeholder", 'Step ' . $count);
        $new_item->nodeValue = $step;
        $dom->getElementById('steps')->appendChild($new_item);
      }
      $count += 1;
    }
  }

  return $dom->saveHTML();
}

function get_recipe_by_id($id, $db_collection) {

  try {
      // Exception will be thrown if ID in URL not in expected format
      $mongodb_id = new \MongoDB\BSON\ObjectId($id);
  } catch (Exception $e) {
      return null;
  }
  return $db_collection->findOne([ '_id' => $mongodb_id]);
}

function compress_image($source_file) {
  $image_info = getimagesize($source_file);
  $width = 900;
  $height = 600;
  $quality = 90;

  if(!empty($image_info)) {
    switch($image_info['mime']) {
      case 'image/jpeg' :
        $image_type = "jpeg";
        // Create a new image from the file or the url.
        $image = imagecreatefromjpeg($source_file);
        $thumb = imagecreatetruecolor($width, $height);
        
        //Resize the $thumb image and save as temp file
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);
        $tempImage = "temp" . $image_type;
        imagejpeg($thumb, $tempImage, $quality);

        break;
      case 'image/png' :
        $image_type = "png";
        // Create a new image from the file or the url.
        $image = imagecreatefrompng($source_file);
        $thumb = imagecreatetruecolor($width, $height);
        
        //Resize the $thumb image and save as temp file
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);
        $tempImage = "temp" . $image_type;
        imagepng($thumb, $tempImage);

        break;
    }
    // Convert image file to binary object
    $resizedImage = file_get_contents($tempImage);
    // Delete temp image file
    unlink($tempImage);
    
    return $resizedImage;

  } else {
    return null;
  }
}
