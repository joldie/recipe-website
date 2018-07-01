<main>
	<div class="flex-container-centered">
		<div class="add-recipe-container">
			<form id="formInput" method="post" enctype="multipart/form-data">
				<h1><?php echo $main_header; ?></h1>
				<h3>Recipe name:</h3>
				<input type="text" class="full-width" id="recipe-name" name="name" maxlength="100" placeholder="Recipe name" value="<?php echo $recipe_name; ?>" autofocus required />
				<h3>Description:</h3>
				<textarea rows="4" class="full-width" name="description" placeholder="Describe the recipe in a sentence or two..."><?php echo $recipe_description; ?></textarea>
				<h3>Tags:</h3>
				<input type="text" name="tags" class="full-width" placeholder="dinner, snack, cold, warm..." value="<?php echo $tags; ?>">
				<script src="js/tags-input.js"></script>
				<script>
					config = {
						escape: [',', ' ', ';'],
						max: 10,
						alert: false
					};
					var tags = new TIB(document.querySelector('input[name="tags"]'), config);
				</script>
				<h3>Image:</h3>
				<p>(max file size: <span id="max-upload"><?php echo $max_upload_size; ?></span> MB)</p>
				<input name="image" type="file" class="img-upload" accept=".jpg, .jpeg, .png">
				<div class="img-preview-div">
					<img class="img-preview" src="<?php echo $image_src; ?>" />
				</div>
				<h3>Serves:</h3>
				<input type="number" class="small-input" name="serves" value="<?php echo $serves; ?>" min="1" max="12" required />
				<span>people</span>
				<h3>Preparation time:</h3>
				<input type="number" class="small-input" name="preptime" value="<?php echo $preptime; ?>" min="0" max="1440" />
				<span>minutes</span>
				<h3>Cooking time:</h3>
				<input type="number" class="small-input" name="cooktime" value="<?php echo $cooktime; ?>" min="0" max="1440" />
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
				<input type="text" class="full-width" name="credit" maxlength="100" placeholder="Name of original source" value="<?php echo $credit; ?>" />
				<input type="text" class="full-width" name="credit_link" maxlength="100" placeholder="Link to website (if applicable)" value="<?php echo $credit_link; ?>" />
				<div class="submit-buttons-div">
					<button id="discard"><i class="fas fa-trash"></i> Cancel</button>
					<button type="submit" name="save"><h3 class="save-button-text"><i class="fas fa-save"></i> Save recipe</h3></button>
				</div>
			</form>
		</div>
	</div>
</main>
