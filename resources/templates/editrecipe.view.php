<main>
	<div class="flex-container-centered">
		<div class="add-recipe-container">
			<form id="formInput" method="post" action="" enctype="multipart/form-data" onsubmit="<?php echo $on_submit_action; ?>">
				<h1 id="main-header"><?php echo $main_header; ?></h1>
				<h3>Recipe name:</h3>
				<input type="text" class="full-width" id="recipe-name" name="name" maxlength="100" placeholder="Recipe name" value="<?php echo $recipe_name; ?>" required />
				<h3>Description:</h3>
				<textarea rows="4" class="full-width" id="recipe-description" name="description" placeholder="Describe the recipe in a sentence or two..."><?php echo $recipe_description; ?></textarea>
				<h3>Image:</h3>
				<p>(max file size: 2 MB)</p>
				<input name="image" id="image" type="file" class="img-upload" accept=".jpg, .jpeg, .png">
				<div class="img-preview-div">
					<img class="img-preview" id="image-preview" src="<?php echo $image_src; ?>" />
				</div>
				<h3>Serves:</h3>
				<input type="number" class="small-input" id="serves" name="serves" value="<?php echo $serves; ?>" min="1" max="12" required />
				<span>people</span>
				<h3>Preparation time:</h3>
				<input type="number" class="small-input" id="preptime" name="preptime" value="<?php echo $preptime; ?>" min="0" max="1440" />
				<span>minutes</span>
				<h3>Cooking time:</h3>
				<input type="number" class="small-input" id="cooktime" name="cooktime" value="<?php echo $cooktime; ?>" min="0" max="1440" />
				<span>minutes</span>
				<h3>Ingredients:</h3>
				<?php echo $ingredients_html; ?>
				<button id="minus-ingredient" class="plusminus"><i class="fas fa-minus"></i></button>
				<button id="plus-ingredient" class="plusminus"><i class="fas fa-plus"></i></button>
				<h3>Method:</h3>
				<div class="method-input">
					<?php echo $steps_html; ?>
					<button id="minus-step" class="plusminus"><i class="fas fa-minus"></i></button>
					<button id="plus-step" class="plusminus"><i class="fas fa-plus"></i></button>
				</div>
				<h3>Credit:</h3>
				<input type="text" class="full-width" id="credit" name="credit" maxlength="100" placeholder="Name of original source" value="<?php echo $credit; ?>" />
				<input type="text" class="full-width" id="credit_link" name="credit_link" maxlength="100" placeholder="Link to website (if applicable)" value="<?php echo $credit_link; ?>" />
				<div class="submit-buttons-div">
					<button type="submit" name="discard" id="discard-button" formnovalidate><i class="fas fa-trash"></i> Cancel</button>
					<button type="submit" name="save" id="save-button"><h3 class="save-button-text"><i class="fas fa-save"></i> Save recipe</h3></button>
				</div>
			</form>
		</div>
	</div>
</main>
