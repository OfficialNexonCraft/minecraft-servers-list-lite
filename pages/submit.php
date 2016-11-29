<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */

User::check_permission(0);
include 'core/functions/recaptchalib.php';

$address = $query_address = $name = $country_code = $youtube_link = $description = null;
$connection_port = $query_port = 25565;

if(!empty($_POST)) {

	/* Define some variables */
	$address = $query_address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
	$connection_port = (int) $_POST['connection_port'];
	$query_port = (int) $_POST['query_port'];
	$date = new DateTime();
	$date = $date->format('Y-m-d H:i:s');
	$active = '0';
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$country_code = (country_check(0, $_POST['country_code'])) ? $_POST['country_code'] : 'US';
	$youtube_link = filter_var($_POST['youtube_id'], FILTER_SANITIZE_STRING);
	$youtube_id = youtube_url_to_id($youtube_link);
	$description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

	$captcha = recaptcha_check_answer ($settings->private_key, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
	$required_fields = array('address', 'connection_port', 'query_port', 'category_id', 'name');

	/* Get category data */
	$category = new StdClass;

	$stmt = $database->prepare("SELECT `category_id`, `name`, `url` FROM `categories` WHERE `category_id` = ?");
	$stmt->bind_param('s', $_POST['category_id']);
	$stmt->execute();
	bind_object($stmt, $category);
	$stmt->fetch();
	$stmt->close();

	/* Determine if category exists */
	if($category !== NULL) {
		$category->exists = true;
	} else {
		$category = new StdClass;
		$category->exists = false;
	}

	/* If the category doesn't exist, set an error message.If it exists, continue with the checks */
	if(!$category->exists) {
		$_SESSION['error'][] = $language['errors']['category_not_found'];
	} else {

		$mcapi_url = 'https://mcapi.us/server/status?ip='.$address.'&port='.$query_port;
		$raw_data = file_get_contents($mcapi_url);
		$info = ($raw_data) ? json_decode($raw_data) : false;


		if(!$info && $info->online) {
			$_SESSION['error'][] = $language['errors']['server_no_data'];
		} else
		if(!$info && !$info->online) {
			$_SESSION['error'][] = $language['errors']['server_offline'];
		}

	}

	/* Check for the required fields */
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $required_fields) == true) {
			$_SESSION['error'][] = $language['errors']['marked_fields_empty'];
			break 1;
		}
	}

	/* More checks */
	if(!$captcha->is_valid) {
		$_SESSION['error'][] = $language['errors']['captcha_not_valid'];
	}
	if(strlen($name) > 64 || strlen($name) < 3) {
		$_SESSION['error'][] = $language['errors']['server_name_length'];
	}
	if(strlen($description) > 2560) {
		$_SESSION['error'][] = $language['errors']['description_too_long'];
	}
	$server = new Server($address, $connection_port);
	if($server->exists) {
		$_SESSION['error'][] = $language['errors']['server_already_exists'];
	}


	/* If there are no errors, add the server to the database */
	if(empty($_SESSION['error'])) {

		/* Add the server to the database as private */
		$stmt = $database->prepare("INSERT INTO `servers` (`user_id`, `category_id`, `address`, `connection_port`, `query_port`, `active`, `status`, `date_added`, `name`, `country_code`, `youtube_id`, `description`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('ssssssssssss',  $account_user_id, $category->category_id, $address, $connection_port, $query_port, $active, $status, $date, $name, $country_code, $youtube_id, $description);
		$stmt->execute();
		$stmt->close();

		/* Set the success message and redirect */
		$_SESSION['success'][] = $language['messages']['server_added'];
		redirect();
	}

display_notifications();

}




?>


<h4><?php echo $language['headers']['submit']; ?></h4>

<form action="" method="post" role="form" enctype="multipart/form-data">
	<div class="form-group">
		<label><?php echo $language['forms']['server_address']; ?> *</label>
		<input type="text" name="address" class="form-control" value="<?php echo $address; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_connection_port']; ?> *</label>
		<p class="help-block"><?php echo $language['forms']['server_connection_port_help']; ?></p>
		<input type="text" name="connection_port" class="form-control" value="<?php echo $connection_port; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_query_port']; ?> *</label>
		<p class="help-block"><?php echo $language['forms']['server_query_port_help']; ?></p>
		<input type="text" name="query_port" class="form-control" value="<?php echo $query_port; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_category']; ?> *</label>
		<select name="category_id" class="form-control">
			<?php
			$result = $database->query("SELECT `category_id`, `name` FROM `categories` ORDER BY `name` ASC");
			while($category = $result->fetch_object()) {
				echo '<option value="' . $category->category_id . '">' . $category->name . '</option>';
			}
			?>
		</select>
	</div>

	<hr />

	<div class="form-group">
		<label><?php echo $language['forms']['server_name']; ?></label>
		<input type="text" name="name" class="form-control" value="<?php echo $name; ?>" />
	</div>


	<div class="form-group">
		<label><?php echo $language['forms']['server_country']; ?></label>
		<select name="country_code" class="form-control">
			<?php country_check(1, $country_code); ?>
		</select>
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_youtube_id']; ?></label>
		<p class="help-block"><?php echo $language['forms']['server_youtube_id_help']; ?></p>
		<input type="text" name="youtube_id" class="form-control" value="<?php echo $youtube_link; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['server_description']; ?></label>
		<p class="help-block"><?php echo $language['forms']['server_description_help']; ?></p>
		<textarea name="description" class="form-control" rows="6"><?php echo $description; ?></textarea>
	</div>

	<div class="form-group">
		  <?php echo recaptcha_get_html($settings->public_key); ?>
	</div>

	<div class="form-group">
		<button type="submit" name="submit" class="btn btn-primary"><?php echo $language['forms']['submit']; ?></button><br /><br />
	</div>

</form>
