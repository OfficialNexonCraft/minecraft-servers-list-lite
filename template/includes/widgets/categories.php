<h5><?php echo $language['misc']['categories']; ?></h5>

<div class="list-group">
	<?php

	$result = $database->query("SELECT `name`, `url`, `category_id` FROM `categories`");
	while($categories = $result->fetch_object()) {

		/* Determine the active category */
		$active = (isset($category) && $category->category_id == $categories->category_id);

		/* Display categories */
		echo '<a href="category/' . $categories->url . '" class="list-group-item list-group-item-side ' . ($active ? "active" : null) . '">' . $categories->name . '</a>';

	}
	?>
</div>
