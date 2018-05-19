<?php

require 'common_top.php';

// Connect to MongoDB database
require 'vendor/autoload.php'; // Include Composer's autoloader
require_once '../db_login.php';
$client = new MongoDB\Client("mongodb://{$db_server}:{$db_port}");

// Test if connection was successful
try {
    $dbs = $client->listDatabases();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Unable to connect to MongoDB. Please check connection string.";
}

// Get recipe ID from URL, sanitise input and create MongoDB ID object
$id = htmlspecialchars($_GET['id']);
try {
    // Exception will be thrown if ID in URL not in expected format
    $mongodb_id = new \MongoDB\BSON\ObjectId($id);
} catch (Exception $e) {
    // Continue execution, regardless
}

// Check if recipe already in DB
$collection = $client->$db_name->$db_collection;
$result = $collection->findOne([ '_id' => $mongodb_id]);

// Only update HTML if recipe in DB, otherwise display default page
if ($result !== null) {
    $dom = new DOMDocument();
    // HTML template for displaying recipe
    $template_html = file_get_contents("recipe.view.php");
    // Options prevent addition of doctype, <html> and <body> tags
    $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $dom->getElementById('recipe-name')->nodeValue = $result['name'];
    $dom->getElementById('recipe-description')->nodeValue = $result['description'];
    $dom->getElementById('serves')->nodeValue = $result['serves'];
    $original_num_serves = $result['serves'];
    $dom->getElementById('preptime')->nodeValue = $result['preptime'];
    $dom->getElementById('cooktime')->nodeValue = $result['cooktime'];
    // Only display credit text/link if value found in DB
    if ($result['credit'] == null) {
      $dom->getElementById('credit_row')->nodeValue = '';
    } elseif ($result['credit_link'] == null) {
      $dom->getElementById('credit_text')->nodeValue = $result['credit'];
    } else {
      $dom->getElementById('credit_text')->nodeValue = '';
      $new_item = $dom->createElement('a', $result['credit']);
      $new_item->setAttribute("href",$result['credit_link']);
      $dom->getElementById('credit_text')->appendChild($new_item);
    }

    foreach ($result['ingredients'] as $ingredient) {
        $new_item = $dom->createElement('li', $ingredient['qty'] . ' ' .
            $ingredient['unit'] . ' ' . $ingredient['item']);
        $dom->getElementById('ingredients-list')->appendChild($new_item);
    }

    foreach ($result['steps'] as $step) {
        $new_item = $dom->createElement('li', $step);
        $dom->getElementById('steps-list')->appendChild($new_item);
    }

    $image_bin = base64_encode($result['image']->getData());
    $dom->getElementById('recipe-image')->setAttribute("src", "data:image/" . $result['image_type'] . ";base64, $image_bin ");


    echo $dom->saveHTML();
} else {
    // Display "recipe not found" page
    require 'recipe_not_found.view.php';
}

echo <<<_END

	<script>

	var MAX_SERVES = 12;

  var originalIngredientQtys = [];

  function getOriginalIngredientQtys(){
    var listItems = $("#ingredients-list li");
    listItems.each(function(idx, li) {
      originalIngredientQtys.push($(li).text().substring(0,$(li).text().indexOf(' ')));
    });
  }

  function updateIngredientQtys(original_num_serves, new_num_serves){
      var listItems = $("#ingredients-list li");
      listItems.each(function(idx, li) {
        var oldVal = $(li).text().substring(0,$(li).text().indexOf(' '));
        var newVal = originalIngredientQtys[idx] * new_num_serves / original_num_serves;
        newVal = Number(newVal.toPrecision(3)).toString();
        $(li).text(newVal + $(li).text().substring($(li).text().indexOf(' '), $(li).text().length));
      });
    }

	// jQuery code on page load
	$(function () {

    getOriginalIngredientQtys();

    $('#plus').click(function(e){
        // Stop acting like a button
        e.preventDefault();

        // Get the current value
        var currentVal = parseInt($('#serves').text());

        var newVal = 0;

        // If is not undefined and not above maximum number of serves, increment
        if (!isNaN(currentVal)) {
            if (currentVal < MAX_SERVES) {
              newVal = currentVal + 1;
              $('#serves').text(newVal);
            }
            else {
              newVal = MAX_SERVES;
            }
        } else {
            // Otherwise set value to 1
            newVal = 1;
            $('#serves').text(newVal);
        }
        updateIngredientQtys($original_num_serves, newVal);
    });

    $('#minus').click(function(e) {
        // Stop acting like a button
        e.preventDefault();

        // Get the current value
        var currentVal = parseInt($('#serves').text());

        var newVal = 0;

        // If it is not undefined or value is greater than 1, decrement
        if (!isNaN(currentVal) && currentVal > 1) {
            newVal = currentVal - 1;
            $('#serves').text(newVal);

        } else {
            // Otherwise set value to 1
            newVal = 1;
            $('#serves').text(newVal);
        }
        updateIngredientQtys($original_num_serves, newVal);
    });

    $('#edit-button-link').attr('href', 'editrecipe.php?id=$id');

	});



	</script>

_END
;

require 'common_bottom.php';
