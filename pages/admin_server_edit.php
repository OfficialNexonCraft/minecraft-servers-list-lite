<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
User::check_permission(1);

/* Check if server exists */
if(!User::x_exists('server_id', $_GET['server_id'], 'servers')) {
	$_SESSION['error'][] = $language['errors']['server_not_found'];
	User::get_back('admin/servers-management');
}

/* Get $server data from the database */
$server = new Server('', '', $_GET['server_id']);

if(isset($_GET['type']) && empty($_POST)) {

	/* Check if the token is valid */
	if(!$token->is_valid()) {
		$_SESSION['error'][] = $language['errors']['invalid_token'];
	}

	/* Check if there are no errors */
	if(empty($_SESSION['error'])) {
		/* Set a success message */
		$_SESSION['success'][] = $language['messages']['success'];

		/* Check for the type of action */
		if($_GET['type'] == 'highlight') {
			$server->new_highlight = ($server->data->highlight) ? 0 : 1;
			$database->query("UPDATE `servers` SET `highlight` = {$server->new_highlight} WHERE `server_id` = {$server->data->server_id}");
		}

		if($_GET['type'] == 'active') {
			$server->new_active = ($server->data->active) ? 0 : 1;
			$database->query("UPDATE `servers` SET `active` = {$server->new_active} WHERE `server_id` = {$server->data->server_id}");
		}

		if($_GET['type'] == 'delete') {
			Server::delete_server($server->data->server_id);
			redirect('admin/servers-management');
		}



		/* Refresh the server data */
		$server = new Server('', '', $_GET['server_id']);

	}

}

if(!empty($_POST)) {
	/* Define some variables */
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
	$connection_port = (int) $_POST['connection_port'];
	$query_port = (int) $_POST['query_port'];
	$category_id = (int) $_POST['category_id'];
	$country_code = (country_check(0, $_POST['country_code'])) ? $_POST['country_code'] : 'US';
	$youtube_id = youtube_url_to_id(filter_var($_POST['youtube_id'], FILTER_SANITIZE_STRING));
	$description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

	/* Check for any errors */
	if(!User::x_exists('category_id', $category_id, $from = 'categories')) {
		$_SESSION['error'][] = $language['errors']['category_not_found'];
	}
	if(strlen($description) > 2560) {
		$_SESSION['error'][] = $language['errors']['description_too_long'];
	}


	/* If there are no errors, proceed with the updating */
	if(empty($_SESSION['error'])) {

		$stmt = $database->prepare("UPDATE `servers` SET `name` = ?, `address` = ?, `connection_port` = ?, `query_port` = ?, `category_id` = ?, `country_code` = ?, `youtube_id` = ?,  `description` = ? WHERE `server_id` = {$server->data->server_id}");
		$stmt->bind_param('ssssssss', $name, $address, $connection_port, $query_port, $category_id, $country_code, $youtube_id, $description);
		$stmt->execute();

		/* Set a success message */
		$_SESSION['success'][] = $language['messages']['success'];

		/* Refresh the server data */
		$server = new Server('', '', $_GET['server_id']);
	}
}

display_notifications();



?>

<h4><?php echo $language['headers']['edit_server']; ?></h4>

<form action="" method="post" role="form" enctype="multipart/form-data">
	<div class="form-group">
		<label><?php echo $language['forms']['server_status']; ?></label>
		<?php
		if($server->data->active)
			echo '<i data-toggle="tooltip" title="' . $language['server']['status_active'] . '" class="fa fa-check green tooltipz"></i>&nbsp;';
		else
			echo '<i data-toggle="tooltip" title="' . $language['server']['status_disabled'] . '" class="fa fa-remove red tooltipz"></i>&nbsp;';
		if($server->data->highlight) echo '<i data-toggle="tooltip" title="' . $language['server']['status_highlighted'] . '" class="fa fa-star tooltipz"></i>&nbsp;';
		?>
	</div>
	<div class="form-group">
		<label><?php echo $language['forms']['server_name']; ?></label>
		<input type="text" name="name" class="form-control" value="<?php echo $server->data->name; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_address']; ?></label>
		<input type="text" name="address" class="form-control" value="<?php echo $server->data->address; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_connection_port']; ?></label>
		<input type="text" name="connection_port" class="form-control" value="<?php echo $server->data->connection_port; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_query_port']; ?></label>
		<input type="text" name="query_port" class="form-control" value="<?php echo $server->data->query_port; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_category']; ?></label>
		<select name="category_id" class="form-control">
			<?php echo '<option value="' . $server->data->category_id . '">' . $language['forms']['server_current_category'] . ' ' . $server->category->name . '</option>'; ?>
			<?php
			$result = $database->query("SELECT `category_id`, `name` FROM `categories` WHERE `category_id` != {$server->data->category_id} ORDER BY `name` ASC");
			while($category = $result->fetch_object()) {
				echo '<option value="' . $category->category_id . '">' . $category->name . '</option>';
			}
			?>
		</select>
	</div>

	<h4><?php echo $language['headers']['edit_server_details']; ?></h4>

	<div class="form-group">
		<label><?php echo $language['forms']['server_date_added']; ?></label>
		<input type="text" class="form-control" value="<?php echo $server->data->date_added; ?>" disabled="true" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_country']; ?></label>
		<select name="country_code" class="form-control">
			<?php country_check(1, $server->data->country_code); ?>
		</select>
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_youtube_id']; ?></label>
		<p class="help-block"><?php echo $language['forms']['server_youtube_id_help']; ?></p>
		<input type="text" name="youtube_id" class="form-control" value="<?php echo $server->data->youtube_id; ?>" />
	</div>


	<div class="form-group">
		<label><?php echo $language['forms']['server_description']; ?></label>
		<p class="help-block"><?php echo $language['forms']['server_description_help']; ?></p>
		<textarea name="description" class="form-control" rows="6"><?php echo $server->data->description; ?></textarea>
	</div>

	<div class="form-group">
		<button type="submit" name="submit" class="btn btn-default"><?php echo $language['forms']['submit']; ?></button>

		<a href="admin/edit-server/<?php echo $_GET['server_id']; ?>/highlight/<?php echo $token->hash; ?>" >
			<button type="button" class="btn btn-warning"><?php echo ($server->data->highlight) ? $language['forms']['server_remove_highlight'] : $language['forms']['server_highlight']; ?></button>
		</a>

		<a href="admin/edit-server/<?php echo $_GET['server_id']; ?>/active/<?php echo $token->hash; ?>" >
			<button type="button" class="btn btn-default"><?php echo ($server->data->active) ? $language['forms']['server_disable'] : $language['forms']['server_activate']; ?></button>
		</a>

		<a href="admin/edit-server/<?php echo $_GET['server_id']; ?>/delete//<?php echo $token->hash; ?>" data-confirm="<?php echo $language['messages']['confirm_delete']; ?>">
			<button type="button" class="btn btn-danger"><?php echo $language['forms']['server_delete']; ?></button>
		</a>

		<br /><br />
	</div>
</form>
