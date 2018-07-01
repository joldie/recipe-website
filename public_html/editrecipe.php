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
  $tags = explode(',', $_POST['tags']);

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
        "width" => 900,
        "height" => 600
    ))->toBuffer();

    $image = new MongoDB\BSON\Binary($resizedImage, MongoDB\BSON\Binary::TYPE_GENERIC);
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

$main_header = "Edit Recipe";
$max_upload_size = $config['max_upload_size_MB'];
$images_path_relative = str_replace(__DIR__ . "/", "", IMAGES_PATH);

// If recipe in DB, display current values in form
$result = get_recipe_by_id(htmlspecialchars($_GET['id']), $collection);
if ($result !== null) {
  $recipe_exists = true;
  $recipe_name = $result['name'];
  $recipe_description = $result['description'];
  $tags = implode(",", (array) $result['tags']);
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
| Load page-specific JavaScript and close body/html tags
|--------------------------------------------------------------------------
*/

echo '<script src="js/editrecipe.js" type="text/javascript"></script>';
echo "\r\n</body>\r\n</html>";
