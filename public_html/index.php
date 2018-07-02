<?php

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| HTML form logic
|--------------------------------------------------------------------------
*/

// Connect to database
require  LIBRARY_PATH . '/connectdb.php';

if (isset($_POST['search'])) {
  // Extract all words from search text (ignoring punctuation, special chars)
  $delimiters = array("\n", "\t", ",", ".", "!", "?", ":", ";", "'", '"');
  $search_string = str_replace($delimiters, " ", $_POST['search']);

  // Search DB collection for recipes based on keywords (limit to 12 results)
  $recipes = $collection->find(['$text'=> ['$search'=>$search_string]],
    ['limit' => 12]);
} elseif (isset($_GET['tag'])) {
  // Get tag from URL, sanitise input and find all recipes tagged with that value
  $tag = htmlspecialchars($_GET['tag']);
  $recipes = $collection->find(['tags' => $tag]);
} else {
  // Retrieve maximum 12 receipes from DB
  $recipes = $collection->find([], ['limit' => 12]);
}

/*
|--------------------------------------------------------------------------
| Populate variables for HTML display
|--------------------------------------------------------------------------
*/

require_once LIBRARY_PATH . "/recipeCardFunctions.php";

$cards_html = generate_cards_html($recipes);

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
