<main>
	<div class='recipe-content-grid'>
		<div class="recipe-content-image">
			<img id='recipe-image' alt='Recipe image' src='<?php echo $recipe_src; ?>' />
		</div>

		<div class='recipe-content-main'>
			<h1><?php echo $recipe_name; ?></h1>
			Tags: <?php echo $tags; ?>
			<p>
				<?php echo $recipe_description; ?>
			</p>

			<table class='recipe-numbers-table'>
				<tr>
					<td>Serves:</td>
					<td>
						<button id="minus" class="plusminus"><i class="fas fa-minus"></i></button>
						<span id='serves'><?php echo $serves; ?></span>
						<button id="plus" class="plusminus"><i class="fas fa-plus"></i></button>
					</td>
				</tr>
				<tr>
					<td>Prep. time:</td>
					<td><span><?php echo $preptime; ?></span> min</td>
				</tr>
				<tr>
					<td>Cooking time:</td>
					<td><span><?php echo $cooktime; ?></span> min</td>
				</tr>
				<tr>
					<td>Credit:</td>
					<td><?php echo $credit_text; ?></td>
				</tr>
			</table>
</div>
	<div class="recipe-content-lists">
		<h3>Ingredients:</h3>
		<ul id='ingredients-list'>
			<?php echo $ingredients; ?>
		</ul>
		<h3>Method:</h3>
		<ol>
			<?php echo $steps; ?>
		</ol>
	</div>
	<div class="recipe-content-lists right-align">
		<a id="edit-button-link" href="index.php">
			<button name="edit"><i class="fas fa-edit"></i> Edit</button>
		</a>
		<button onclick="confirmDelete();"><i class="fas fa-trash"></i> Delete</button>
		</div>

<!-- HashOver commenting system JS script -->
<?php
	echo "\r\n<script src='vendor/hashover/comments.php' type='text/javascript'></script>";
	echo "\r\n<noscript>You must have JavaScript enabled to use the comments.</noscript>";
?>

<!-- schema.org JSON-LD data -->
<?php
		$keywords = "";
		if ($result['tags'] != null) {
		  foreach ($result['tags'] as $tag) {
			  $keywords = $tag . "," . $keywords;
		  }
		}

        $schemadata = array(
        "@context" => "http://schema.org",
        "@type" => "Recipe",
        "name" => $recipe_name,
        "description" => $recipe_description,
		"keywords" => $keywords,
        "image" => "",
        "prepTime" => "PT" . $preptime . "M",
        "cookTime" => "PT" . $cooktime . "M",
        "recipeYield" => intval($serves),
        "recipeIngredient" => $ingredients_text,
		"recipeInstructions" => $result['steps'],
		"url" => "https://recipes-rebooted.com/recipe.php?id=" . $id
        );

        echo "\r\n<script type='application/ld+json'>";
        echo json_encode($schemadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        echo "\r\n</script>";
?>

</div>
</main>
