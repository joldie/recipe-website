<main>
	<div class='recipe-content-grid'>
		<div class="recipe-content-image">
			<img id='recipe-image' alt='Recipe image' src='<?php echo $recipe_src; ?>' />
		</div>

		<div class='recipe-content-main'>
			<h1 id='recipe-name'><?php echo $recipe_name; ?></h1>
			<p id='recipe-description'>
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
					<td><span id='preptime'><?php echo $preptime; ?></span> min</td>
				</tr>
				<tr>
					<td>Cooking time:</td>
					<td><span id='cooktime'><?php echo $cooktime; ?></span> min</td>
				</tr>
				<tr id="credit_row">
					<td>Credit:</td>
					<td id="credit_text"><?php echo $credit_text; ?></td>
				</tr>
			</table>
</div>
	</div>
	<div class="recipe-content-lists">
		<h3>Ingredients:</h3>
		<ul id='ingredients-list'>
			<?php echo $ingredients; ?>
		</ul>
				<h3>Method:</h3>
				<ol id='steps-list'>
					<?php echo $steps; ?>
				</ol>
			</div>
			<div class="recipe-content-lists right-align">
				<a id="edit-button-link" href="index.php">
					<button name="edit" id="edit-button"><i class="fas fa-edit"></i> Edit</button>
				</a>
			</div>
</main>
