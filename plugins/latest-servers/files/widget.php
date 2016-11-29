<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
$config_limit = Plugin::$plugins['latest-servers']['limit'];
?>

<h5><?php echo $language['misc']['latest_servers']; ?></h5>
<div class="list-group">
	<?php
	$result = $database->query("SELECT `server_id`, `address`, `connection_port`, `status`,`cachetime` FROM `servers` ORDER BY `server_id` DESC LIMIT {$config_limit}");
	while($my_servers = $result->fetch_object()):
	?>
	<a class="list-group-item list-group-item-side" href="server/<?php echo $my_servers->address . ':' . $my_servers->connection_port; ?>">
		<?php echo $my_servers->address; ?>
	</a>
	<?php endwhile; ?>
</div>
