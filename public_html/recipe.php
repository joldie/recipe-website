<?php

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| Populate variables for HTML display
|--------------------------------------------------------------------------
*/

// Connect to database
require  LIBRARY_PATH . '/connectdb.php';

// Get recipe ID from URL, sanitise input and create MongoDB ID object
$id = htmlspecialchars($_GET['id']);
try {
    // Exception will be thrown if ID in URL not in expected format
    $mongodb_id = new \MongoDB\BSON\ObjectId($id);
} catch (Exception $e) {
    // Continue execution, regardless
}

// Check if recipe already in DB
$result = $collection->findOne([ '_id' => $mongodb_id]);

if ($result !== null) {
  $recipe_exists = true;

  $image_bin = base64_encode($result['image']->getData());
  $recipe_src = "data:image/" . $result['image_type'] . ";base64, $image_bin ";

  $recipe_name = $result['name'];

  $recipe_description = $result['description'];
  if ($recipe_description == "") { $recipe_description = "No description."; }

  $serves = $result['serves'];
  $original_num_serves = $result['serves'];
  $preptime = $result['preptime'];
  $cooktime = $result['cooktime'];

  if ($result['credit'] == null) {
    $credit_text = '-';
  } elseif ($result['credit_link'] == null) {
    $credit_text = $result['credit'];
  } else {
    $credit_text = "<a href='{$result['credit_link']}'>{$result['credit']}</a>";
  }

  $ingredients = "";
  foreach ($result['ingredients'] as $ingredient) {
      $ingredients = $ingredients . "<li>{$ingredient['qty']} {$ingredient['unit']} {$ingredient['item']}</li>";
  }

  $steps = "";
  foreach ($result['steps'] as $step) {
      $steps = $steps . "<li>$step</li>";
  }
} else {
  $recipe_exists = false;
}

/*
|--------------------------------------------------------------------------
| Load HTML views
|--------------------------------------------------------------------------
*/

require TEMPLATES_PATH . '/header.view.php';
if ($recipe_exists) { require TEMPLATES_PATH . '/recipe.view.php'; }
else { require TEMPLATES_PATH . '/recipenotfound.view.php'; }
require TEMPLATES_PATH . '/footer.view.php';

/*
|--------------------------------------------------------------------------
| JS scripts
|--------------------------------------------------------------------------
*/

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
