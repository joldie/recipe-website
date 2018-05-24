<?php

require 'common_top.php';

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

if (isset($_POST['discard'])) {
  // Redirect back to recipe page
  echo "<script> window.location.replace('recipe.php?id={$_GET['id']}') </script>";
  die();
} elseif (isset($_POST['name'])) {
  // If user inputted data, check and insert into database
  $name = $_POST['name'];
  $date = date("Y-m-d H:i:s", time());
  $description = $_POST['description'];
  $serves = $_POST['serves'];
  $preptime = $_POST['preptime'];
  $cooktime = $_POST['cooktime'];
  $credit = $_POST['credit'];
  $credit_link = $_POST['credit_link'];
  //$tags = ['dinner', 'pasta'];

  $num_ingredients = 0;
  $ingredients = [];
  while (isset($_POST['item' . ($num_ingredients + 1)])) {
    $num_ingredients += 1;
    $new_ingredient = [ 'qty' => $_POST['qty' . $num_ingredients], 'unit' => $_POST['unit' . $num_ingredients], 'item' => $_POST['item' . $num_ingredients] ];
    $ingredients[count($ingredients)] = $new_ingredient;
  }

  $num_steps = 0;
  $steps = [];
  while (isset($_POST['step' . ($num_steps + 1)])) {
    $num_steps += 1;
    $new_step = $_POST['step' . $num_steps];
    $steps[count($steps)] = $new_step;
  }

  if ($_FILES['image']['name'] !== '') {
    $image = new MongoDB\BSON\Binary(file_get_contents($_FILES['image']['tmp_name']), MongoDB\BSON\Binary::TYPE_GENERIC);
    $image_type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
  }
  else {
    // No new image selected by user, so leave unchanged
    $image = '';
  }

  // Update recipe in DB
  $id = htmlspecialchars($_GET['id']);
  $mongodb_id = new \MongoDB\BSON\ObjectId($id);
  $result = $collection->updateOne([ '_id' => $mongodb_id],[
    '$set' => [
    'name' => $name,
    'date' => $date,
    'description' => $description,
    'serves' => $serves,
    'preptime' => $preptime,
    'cooktime' => $cooktime,
    'credit' => $credit,
    'credit_link' => $credit_link,
    'tags' => $tags,
    'ingredients' => $ingredients,
    'steps' => $steps
  ]]);
  // Only update image if new one selected by user
  if ($image !== '') {
    $result = $collection->updateOne([ '_id' => $mongodb_id],[
      '$set' => [
  		'image' => $image,
  		'image_type' => $image_type
  	]]);
  }

  echo "<script> alert('Recipe successfully saved.') </script>";

  // Redirect to newly created recipe page
  echo "<script> window.location.replace('recipe.php?id={$_GET['id']}') </script>";
  die();

}

// Get recipe ID from URL (if available), sanitise input and create MongoDB ID object
$id = htmlspecialchars($_GET['id']);
try {
    // Exception will be thrown if ID in URL not in expected format
    $mongodb_id = new \MongoDB\BSON\ObjectId($id);
} catch (Exception $e) {
    // Continue execution, regardless
}

if ($mongodb_id !== null) {

  $result = $collection->findOne([ '_id' => $mongodb_id]);

  // If recipe in DB, display current values in form
  if ($result !== null) {
    $dom = new DOMDocument();
    // HTML template for displaying recipe
    $template_html = file_get_contents("addrecipe.view.php");
    // Options prevent addition of doctype, <html> and <body> tags
    $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $dom->getElementById('main-header')->nodeValue = "Edit Recipe";
    $dom->getElementById('recipe-name')->setAttribute("value", $result['name']);
    $dom->getElementById('recipe-description')->nodeValue = $result['description'];
    $dom->getElementById('serves')->setAttribute("value", $result['serves']);
    $dom->getElementById('preptime')->setAttribute("value", $result['preptime']);
    $dom->getElementById('cooktime')->setAttribute("value", $result['cooktime']);
    $dom->getElementById('credit')->setAttribute("value", $result['credit']);
    $dom->getElementById('credit_link')->setAttribute("value", $result['credit_link']);

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

    $image_bin = base64_encode($result['image']->getData());
    $dom->getElementById('image-preview')->setAttribute("src", "data:image/" . $result['image_type'] . ";base64, $image_bin ");

    echo $dom->saveHTML();

  } else {
    // Display blank add recipe page
    require 'addrecipe.view.php';
  }
} else {
  // Display blank add recipe page
  require 'addrecipe.view.php';
}

echo <<<_END

	<script>

	var MAX_INPUT = 20;

	// jQuery code on page load
	$(function () {

    // Set focus to first text input
    $('#name').focus();

    $('#plus-ingredient').click(function(e){
        // Stop acting like a button
        e.preventDefault();

        // Get the current value
        var currentVal = $('.ingredient-input').length;

        // If is not undefined and not above maximum number of inputs, add new input
        if (!isNaN(currentVal)) {
            if (currentVal < MAX_INPUT) {
              $('#ingredient1')
	                    .clone()
                      .attr({'id': 'ingredient'+ (currentVal+1), 'name': 'ingredient'+ (currentVal+1)})
	                    .insertAfter('[id^=ingredient]:last');
              $('[id^=ingredient]:last').children('#qty1').attr({'id': 'qty'+ (currentVal+1), 'name': 'qty'+ (currentVal+1)});
              $('[id^=ingredient]:last').children('[id^=qty]').val('');
              $('[id^=ingredient]:last').children('#unit1').attr({'id': 'unit'+ (currentVal+1), 'name': 'unit'+ (currentVal+1)});
              $('[id^=ingredient]:last').children('[id^=unit]').val('');
              $('[id^=ingredient]:last').children('#item1').attr({'id': 'item'+ (currentVal+1), 'name': 'item'+ (currentVal+1)});
              $('[id^=ingredient]:last').children('[id^=item]').val('');
            }
        }
    });

    $('#minus-ingredient').click(function(e) {
        // Stop acting like a button
        e.preventDefault();

        // Get the current value
        var currentVal = $('.ingredient-input').length;

        var newVal = 0;

        // If it is not undefined or value is greater than 1, remove input
        if (!isNaN(currentVal) && currentVal > 1) {
            $('#ingredient'+(currentVal)).remove();

        }
    });

    $('#plus-step').click(function(e){
        // Stop acting like a button
        e.preventDefault();

        // Get the current value
        var currentVal = $('.step-input').length;

        // If is not undefined and not above maximum number of inputs, add new input
        if (!isNaN(currentVal)) {
            if (currentVal < MAX_INPUT) {
              $('#step1')
	                    .clone()
                      .attr({'id': 'step'+ (currentVal+1), 'name': 'step'+ (currentVal+1), 'placeholder': 'Step '+ (currentVal+1)})
                      .val('')
	                    .insertAfter('[id^=step]:last');
            }
        }
    });

    $('#minus-step').click(function(e) {
        // Stop acting like a button
        e.preventDefault();

        // Get the current value
        var currentVal = $('.step-input').length;

        var newVal = 0;

        // If it is not undefined or value is greater than 1, remove input
        if (!isNaN(currentVal) && currentVal > 1) {
            $('#step'+(currentVal)).remove();

        }
    });

    var imgInput = document.querySelector('.img-upload');
    imgInput.addEventListener('change', updateImageDisplay);

    function updateImageDisplay() {
      var selectedFiles = imgInput.files;
      if(selectedFiles.length === 1) {
        if (selectedFiles[0].size > 2000000) {
          alert("Image file size exceeds 2MB. Please select another image.");
          imgInput.value = '';
          $('.img-preview').attr('src', 'images/image.png');
        } else {
          $('.img-preview').attr('src', window.URL.createObjectURL(selectedFiles[0]));
        }
      }
    }

	});

  function checkFormData() {
    // No additional checks required (yet)
    return true;
  }

	</script>

_END
;

require 'common_bottom.php';
