<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
User::check_permission(1);

if(isset($_GET['delete'])) {

	/* Check for errors */
	if(!$token->is_valid()) {
		$_SESSION['error'][] = $language['errors']['invalid_token'];
	}

	if(empty($_SESSION['error'])) {
		/* Get the $server_id from the $category_id */
		$server_id = User::x_to_y('category_id', 'server_id', $_GET['delete'], 'servers');

		/* Delete category and all servers from that category */
		$database->query("DELETE FROM `categories` WHERE `category_id` = {$_GET['delete']}");

		$result = $database->query("SELECT `server_id` FROM `servers` WHERE `category_id` = {$_GET['delete']}");
		while($servers = $result->fetch_object()) Server::delete_server($servers->server_id);

		/* Set the success message & redirect*/
		$_SESSION['success'][] = $language['messages']['success'];
		User::get_back('admin/categories-management');
	}
}

if(!empty($_POST)) {
	/* Define some variables */
	$_POST['name']				 		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$_POST['title']				 		= filter_var($_POST['title'], FILTER_SANITIZE_STRING);
	$_POST['description']		 		= filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$_POST['url']						= generateSlug(filter_var($_POST['url'], FILTER_SANITIZE_STRING));
	$required_fields = array('name', 'url');


	/* Check for the required fields */
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $required_fields) == true) {
			$_SESSION['error'][] = $language['errors']['marked_fields_empty'];
			break 1;
		}
	}

	/* If there are no errors continue the updating process */
	if(empty($_SESSION['error'])) {

		$stmt = $database->prepare("INSERT INTO `categories` (`name`, `title`, `description`, `url`) VALUES ( ?, ?, ?, ?)");
		$stmt->bind_param('ssss', $_POST['name'], $_POST['title'], $_POST['description'], $_POST['url']);
		$stmt->execute();
		$stmt->close();

		$_SESSION['success'][] = $language['messages']['success'];
	}

	display_notifications();

}




?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Url</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$result = $database->query("SELECT `category_id`, `name`, `url` FROM `categories` ORDER BY `category_id` ASC");
					while($category = $result->fetch_object()):
					?>
						<tr>
							<td><?php echo $category->name; ?></td>
							<td><a href="category/<?php echo $category->url; ?>"><?php echo $category->url; ?></td>
							<td><?php category_admin_buttons($category->category_id, $token->hash); ?></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>



<h4><?php echo $language['forms']['admin_add_category_header']; ?></h4>

<form action="" method="post" role="form">
	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_name']; ?> *</label>
		<input type="text" name="name" class="form-control" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_title']; ?></label>
		<p class="help-block"><?php echo $language['forms']['admin_add_category_title_help']; ?></p>
		<input type="text" name="title" class="form-control" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_description']; ?></label>
		<p class="help-block"><?php echo $language['forms']['admin_add_category_description_help']; ?></p>
		<input type="text" name="description" class="form-control" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_url']; ?> *</label>
		<p class="help-block"><?php echo $language['forms']['admin_add_category_url_help']; ?></p>
		<input type="text" name="url" class="form-control" />
	</div>


	<button type="submit" name="submit" class="btn btn-primary"><?php echo $language['forms']['submit']; ?></button>
</form>
