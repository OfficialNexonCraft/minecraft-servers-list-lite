<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
User::check_permission(1);

/* Check if category exists */
if(!User::x_exists('category_id', $_GET['category_id'], 'categories')) {
	$_SESSION['error'][] = $language['errors']['category_not_found'];
	User::get_back('admin/categories-management');
}

if(!empty($_POST)) {
	/* Define some variables */
	$_POST['name']				 		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$_POST['title']				 		= filter_var($_POST['title'], FILTER_SANITIZE_STRING);
	$_POST['description']				= filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$_POST['url']						= generateSlug(filter_var($_POST['url'], FILTER_SANITIZE_STRING));
	$_GET['category_id']				= (int)$_GET['category_id'];
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

		$stmt = $database->prepare("UPDATE `categories` SET  `name` = ?, `title` = ?, `description` = ?, `url` = ? WHERE `category_id` = ?");
		$stmt->bind_param('sssss', $_POST['name'], $_POST['title'], $_POST['description'], $_POST['url'], $_GET['category_id']);
		$stmt->execute();
		$stmt->close();

		/* Set a success message */
		$_SESSION['success'][] = $language['messages']['success'];
	}

	display_notifications();

}

/* Get $category data from the database */
$stmt = $database->prepare("SELECT * FROM `categories` WHERE `category_id` = ?");
$stmt->bind_param('s', $_GET['category_id']);
$stmt->execute();
bind_object($stmt, $category);
$stmt->fetch();
$stmt->close();



?>


<h4><?php echo $language['headers']['edit_category']; ?></h4>

<form action="" method="post" role="form" enctype="multipart/form-data">
	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_name']; ?></label>
		<input type="text" name="name" class="form-control" value="<?php echo $category->name; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_title']; ?></label>
		<p class="help-block"><?php echo $language['forms']['admin_add_category_title_help']; ?></p>
		<input type="text" name="title" class="form-control"value="<?php echo $category->title; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_description']; ?></label>
		<p class="help-block"><?php echo $language['forms']['admin_add_category_description_help']; ?></p>
		<input type="text" name="description" class="form-control" value="<?php echo $category->description; ?>"/>
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['admin_add_category_url']; ?></label>
		<p class="help-block"><?php echo $language['forms']['admin_add_category_url_help']; ?></p>
		<input type="text" name="url" class="form-control" value="<?php echo $category->url; ?>" />
	</div>

	<button type="submit" name="submit" class="btn btn-primary"><?php echo $language['forms']['submit']; ?></button>
</form>
