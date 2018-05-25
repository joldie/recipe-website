<?php

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| HTML form logic
|--------------------------------------------------------------------------
*/

// Connect to database
require  LIBRARY_PATH . '/connectdb.php';

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

/*
|--------------------------------------------------------------------------
| Populate variables for HTML display
|--------------------------------------------------------------------------
*/

require_once LIBRARY_PATH . "/editRecipeFunctions.php";

$main_header = "Edit Recipe";
$images_path_relative = str_replace(__DIR__ . "/", "", IMAGES_PATH);

// If recipe in DB, display current values in form
$result = get_recipe_by_id(htmlspecialchars($_GET['id']), $collection);
if ($result !== null) {
  $recipe_exists = true;
  $recipe_name = $result['name'];
  $recipe_description = $result['description'];
  $image_bin = base64_encode($result['image']->getData());
  $image_src = "data:image/" . $result['image_type'] . ";base64, $image_bin ";
  $serves = $result['serves'];
  $preptime = $result['preptime'];
  $cooktime = $result['cooktime'];
  $ingredients_html = generate_ingredients_html($result);
  $steps_html = generate_steps_html($result);
  $credit = $result['credit'];
  $credit_link = $result['credit_link'];
} else {
  $recipe_exists = false;
}

/*
|--------------------------------------------------------------------------
| Load HTML views
|--------------------------------------------------------------------------
*/

require TEMPLATES_PATH . '/header.view.php';
if ($recipe_exists) { require TEMPLATES_PATH . '/editrecipe.view.php'; }
else { require TEMPLATES_PATH . '/recipenotfound.view.php'; }
require TEMPLATES_PATH . '/footer.view.php';

/*
|--------------------------------------------------------------------------
| JS scripts
|--------------------------------------------------------------------------
*/

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
          $('.img-preview').attr('src', '{$images_path_relative}/image.png');
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
