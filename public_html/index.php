<?php

header("Cache-Control: max-age=86400, must-revalidate");

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| HTML form logic
|--------------------------------------------------------------------------
*/

// Connect to database
require  LIBRARY_PATH . '/connectdb.php';

const INITIAL_DISPLAY_COUNT = 12;

if (isset($_POST['search'])) {
  // Extract all words from search text (ignoring punctuation, special chars)
  $delimiters = array("\n", "\t", ",", ".", "!", "?", ":", ";", "'", '"');
  $search_string = str_replace($delimiters, " ", $_POST['search']);

  // Search DB collection for recipes based on keywords
  // First, retrieve list of recipe IDs and names
  $all_recipe_ids = $collection->find(['$text'=> ['$search'=>$search_string]], ['_id'=>true, 'name'=>true, 'sort'=>['_id'=>-1]]);

  // Retrieve recipes, up to initial display maximum
  $recipes = $collection->find(['$text'=> ['$search'=>$search_string]],
    ['limit' => INITIAL_DISPLAY_COUNT, 'sort'=>['_id'=>-1]]);

} elseif (isset($_GET['tag'])) {
  // Get tag from URL, sanitise input and find all recipes tagged with that value
  $tag = htmlspecialchars($_GET['tag']);

  // Search DB collection for recipes based on tags
  // First, retrieve list of recipe IDs and names
  $all_recipe_ids = $collection->find(['tags' => $tag], ['_id'=>true, 'name'=>true, 'sort'=>['_id'=>-1]]);

  // Retrieve recipes, up to initial display maximum
  $recipes = $collection->find(['tags' => $tag], ['limit' => INITIAL_DISPLAY_COUNT, 'sort'=>['_id'=>-1]]);

} else {

  // Search DB collection for all recipes
  // First, retrieve list of recipe IDs and names
  $all_recipe_ids = $collection->find([], ['_id'=>true, 'name'=>true, 'sort'=>['_id'=>-1]]);
  
  // Retrieve recipes, up to initial display maximum
  $recipes = $collection->find([], ['limit' => INITIAL_DISPLAY_COUNT, 'sort'=>['_id'=>-1]]);
  
}

/*
|--------------------------------------------------------------------------
| Populate variables for HTML display
|--------------------------------------------------------------------------
*/

require_once LIBRARY_PATH . "/recipeCardFunctions.php";

$cards_html = generate_cards_html($recipes, $all_recipe_ids);

/*
|--------------------------------------------------------------------------
| Load HTML views
|--------------------------------------------------------------------------
*/

require TEMPLATES_PATH . '/header.view.php';
require TEMPLATES_PATH . '/index.view.php';
require TEMPLATES_PATH . '/footer.view.php';

/*
|--------------------------------------------------------------------------
| Load page-specific JavaScript and close body/html tags
|--------------------------------------------------------------------------
*/

echo '<script src="js/index.js" type="text/javascript"></script>';
echo "\r\n</body>\r\n</html>";
