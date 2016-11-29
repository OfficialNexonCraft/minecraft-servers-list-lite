<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
include '../core/init.php';

$_POST['limit'] = (int) $_POST['limit'];
$results_limit = 25;

$result = $database->query("SELECT `server_id`, `category_id`, `address`, `connection_port`, `query_port`, `date_added`, `active`, `highlight` FROM `servers` ORDER BY `server_id` DESC LIMIT {$_POST['limit']}, {$results_limit}");
while($servers_data = $result->fetch_object()) {
?>
<tr>
	<td>
		<?php
		if($servers_data->active)
			echo '<i data-toggle="tooltip" title="' . $language['server']['status_active'] . '" class="fa fa-check green tooltipz"></i>&nbsp;';
		else
			echo '<i data-toggle="tooltip" title="' . $language['server']['status_disabled'] . '" class="fa fa-remove red tooltipz"></i>&nbsp;';
		if($servers_data->highlight) echo '<span data-toggle="tooltip" title="' . $language['server']['status_highlighted'] . '" class="glyphicon glyphicon-star tooltipz"></span>&nbsp;';
		?>
	</td>
	<td><?php echo $servers_data->address; ?></td>
	<td><?php echo $servers_data->connection_port; ?></td>
	<td><?php echo Server::get_category($servers_data->category_id); ?></td>
	<td><a href="admin/edit-server/<?php echo $servers_data->server_id; ?>"><i class="fa fa-pencil"></i></a></td>
</tr>
<?php } ?>

<?php if($result->num_rows == $results_limit) { ?>
<tr id="showMoreServers">
	<td colspan="6">
		<div class="center">
			<button id="showMore" class="btn btn-default" onClick="showMore(<?php echo $_POST['limit'] + $results_limit; ?>, 'processing/admin_servers_show_more.php', '#results', '#showMoreServers');"><?php echo $language['misc']['show_more']; ?></button>
		</div>
	</td>
</tr>
<?php } ?>
