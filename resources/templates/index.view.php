<main>
	<div class="content-header">
		<h2>Reboot your mealtime with</h2>
		<h1>Simple, Tasty <small>and</small> 100% Plant-Based <small>recipes</small></h1>
		<form method="post" onsubmit="return checkFormData();">
			<input type="text" class="searchbox" id="searchInput" name="search" placeholder="Search for dinner now...">
		</form>
	</div>
	<div class="grid-container">
		<?php echo $cards_html; ?>
	</div>
	<div id="div-load-more" onclick="return loadMoreRecipes();">
		<button id="button-load-more">Load more recipes...</button>
	</div>
</main>
