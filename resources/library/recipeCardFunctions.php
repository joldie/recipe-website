<?php

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| Helper functions for displaying recipe cards
|--------------------------------------------------------------------------
*/

function create_recipe_card($recipe) {

  $dom = new DOMDocument();
  // Load HTML template
  $template_html = file_get_contents(TEMPLATES_PATH . '/card.view.php');
  // Options prevent addition of doctype, <html> and <body> tags
  $dom->loadHTML($template_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  // Set HTML values
  $dom->getElementById('card-title')->nodeValue = $recipe['name'];
  $recipe_id = (string)$recipe['_id'];
  $dom->getElementById('card-link')->setAttribute("href", "recipe.php?id={$recipe_id}");
  $image_bin = base64_encode($recipe['image']->getData());
  $image_url = "data:image/" . $recipe['image_type'] . ";base64," . $image_bin;
  $dom->getElementById('card-image')->setAttribute("style", "background-image: url({$image_url})");

  // Return updated HTML
  return $dom->saveHTML();
}

function generate_cards_html($recipes) {

  $html = "";
  $count_recipes = 0;

  // For each recipe, display a "card" div
  foreach ($recipes as $recipe) {
    $count_recipes += 1;
    $html = $html . create_recipe_card($recipe);
  }

  if ($count_recipes == 0) {
    $html = "<h4>No recipes found<br /><br /> Try again or <a href='index.php' style='text-decoration:underline'>return home</a></h4>";
  }

  return $html;

}
