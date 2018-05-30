<?php

/*
|--------------------------------------------------------------------------
| Load config file and libraries
|--------------------------------------------------------------------------
*/

require_once '../config.php';
require_once LIBRARY_PATH . "/editRecipeFunctions.php";

/*
|--------------------------------------------------------------------------
| HTML form logic
|--------------------------------------------------------------------------
*/

// Connect to database
require  LIBRARY_PATH . '/connectdb.php';

if (isset($_POST['name'])) {
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
    // Resize uploaded image using tinify API
    require_once 'vendor/autoload.php'; // Include Composer's autoloader
    \Tinify\setKey($config['tinify_api_key']);

    $imageData = \Tinify\fromBuffer(file_get_contents($_FILES['image']['tmp_name']));
    $resizedImage = $imageData->resize(array(
        "method" => "fit",
        "width" => 1500,
        "height" => 1000
    ))->toBuffer();

    $image = new MongoDB\BSON\Binary($resizedImage, MongoDB\BSON\Binary::TYPE_GENERIC);
    $image_type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
  }
  else {
    // Upload dummy image
    $image = new MongoDB\BSON\Binary(file_get_contents(IMAGES_PATH . "/image.png"), MongoDB\BSON\Binary::TYPE_GENERIC);
    $image_type = 'png';
  }

  try {
    // Insert recipe into DB
    $result = $collection->insertOne([
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
      'steps' => $steps,
  		'image' => $image,
  		'image_type' => $image_type
  	]);
  } catch (\Exception $e) {
    echo "7The error message is: " . $e->getMessage();
  }


  echo "<script> alert('Recipe successfully saved.') </script>";

  // Redirect to newly created recipe page
  echo "<script> window.location.replace('recipe.php?id={$result->getInsertedId()}') </script>";
  die();

}

/*
|--------------------------------------------------------------------------
| Populate variables for HTML display
|--------------------------------------------------------------------------
*/

$main_header = "Add Recipe";
$recipe_name = "";
$recipe_description = "";
$images_path_relative = str_replace(__DIR__ . "/", "", IMAGES_PATH);
$image_src = $images_path_relative . "/image.png";
$serves = "1";
$preptime = "10";
$cooktime = "10";
$ingredients_html = generate_ingredients_html(null);
$steps_html = generate_steps_html(null);
$credit = "";
$credit_link = "";

/*
|--------------------------------------------------------------------------
| Load HTML views
|--------------------------------------------------------------------------
*/

require TEMPLATES_PATH . '/header.view.php';
require TEMPLATES_PATH . '/editrecipe.view.php';
require TEMPLATES_PATH . '/footer.view.php';

/*
|--------------------------------------------------------------------------
| Load page-specific JavaScript and close body/html tags
|--------------------------------------------------------------------------
*/

echo '<script src="js/addrecipe.js" type="text/javascript"></script>';
echo "\r\n</body>\r\n</html>";
