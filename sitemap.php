<?php
header('Content-type: text/xml');

include 'core/init.php';


?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<url>
		<loc><?php echo $settings->url . 'index'; ?></loc>
	</url>

	<url>
		<loc><?php echo $settings->url . 'login'; ?></loc>
	</url>

	<url>
		<loc><?php echo $settings->url . 'servers'; ?></loc>
	</url>

	<?php
	$result = $database->query("SELECT `address`, `connection_port` FROM `servers` WHERE `active` = '1'");
	while($server = $result->fetch_object()){
	?>

	<url>
		<loc><?php echo $settings->url . 'server/' . $server->address . ':' . $server->connection_port; ?></loc>
	</url>

	<?php }	?>

	<?php
	$result = $database->query("SELECT `url` FROM `categories`");
	while($category = $result->fetch_object()){
	?>

	<url>
		<loc><?php echo $settings->url . 'category/' . $category->url; ?></loc>
	</url>

	<?php }	?>

</urlset>
