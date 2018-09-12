<?php

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| Helper functions for displaying recipe cards
|--------------------------------------------------------------------------
*/

function create_recipe_card($recipe, $empty) {

  $dom = new DOMDocument();
  // Load HTML template
  $template_html = file_get_contents(TEMPLATES_PATH . '/card.view.php');
  // Options prevent addition of doctype, <html> and <body> tags
  $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  // Set HTML values
  $recipe_id = (string)$recipe['_id'];
  $dom->getElementById('card-link')->setAttribute("href", "recipe.php?id={$recipe_id}");
  $dom->getElementById('card-title')->nodeValue = $recipe['name'];
  $dom->getElementById('card-link')->setAttribute("data-image-loaded", "false");
  if (!$empty) {
    $dom->getElementById('card-link')->setAttribute("data-image-loaded", "true");
    $image_bin = base64_encode($recipe['image']->getData());
    $image_url = "data:image/" . $recipe['image_type'] . ";base64," . $image_bin;
    $dom->getElementById('card-image')->setAttribute("style", "background-image: url({$image_url})");
  }

  // Return updated HTML
  return $dom->saveHTML();
}

function generate_cards_html($recipes, $all_recipe_ids) {

  $html = "";
  $count_recipes = 0;

  // For each recipe with full data, display a "card" div
  foreach ($recipes as $recipe) {
    $count_recipes += 1;
    $html = $html . create_recipe_card($recipe, false);
  }

  // Add empty cards for remaining recipes, including only recipe ID
  $all_recipes = 0;
  foreach ($all_recipe_ids as $recipe_id) {
    $all_recipes++;
    if ($all_recipes > $count_recipes) {
      $html = $html . create_recipe_card($recipe_id, true);
    }
  }

  if ($count_recipes == 0) {
    $html = "<h4>No recipes found<br /><br /> Try again or <a href='index.php' style='text-decoration:underline'>return home</a></h4>";
  }

  return $html;

}
