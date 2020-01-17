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

  $tags = "";
  if ($result['tags'] != null) {
    foreach ($result['tags'] as $tag) {
        $tags = $tags . "<a href='index.php?tag=$tag'><span class='tag-display'>$tag</span></a>";
    }
  }

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
  $ingredients_text = array();
  foreach ($result['ingredients'] as $ingredient) {
      $ingredients = $ingredients . "<li>{$ingredient['qty']} {$ingredient['uni$
      array_push($ingredients_text, trim(str_replace("  ", " ", $ingredient['qt$
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
| Load page-specific JavaScript and close body/html tags
|--------------------------------------------------------------------------
*/

echo '<script src="js/recipe.js" type="text/javascript"></script>';
echo "\r\n</body>\r\n</html>";
