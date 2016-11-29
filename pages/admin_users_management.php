<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
User::check_permission(1);

if(!empty($_POST)) {
	/* Clean some posted variables */
	$_POST['username']	= filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

	/* Define some variables */
	$fields = array('username', 'email' ,'password', 'repeat_password');

	/* Check for any errors */
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true) {
			$_SESSION['error'][] = $language['errors']['fields_required'];
			break 1;
		}
	}

	if(strlen($_POST['username']) > 32 || strlen($_POST['username']) < 3) {
		$_SESSION['error'][] = $language['errors']['username_length'];
	}
	if(User::x_exists('username', $_POST['username'])) {
		$_SESSION['error'][] = sprintf($language['errors']['user_exists'], $_POST['username']);
	}
	if(User::x_exists('email', $_POST['email'])) {
		$_SESSION['error'][] = $language['errors']['email_used'];
	}
	if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
		$_SESSION['error'][] = $language['errors']['invalid_email'];
	}
	if(strlen(trim($_POST['password'])) < 6) {
        $_SESSION['error'][] = $language['errors']['password_too_short'];
    }
    if($_POST['password'] !== $_POST['repeat_password']) {
        $_SESSION['error'][] = $language['errors']['passwords_doesnt_match'];
    }


	/* If there are no errors continue the registering process */
	if(empty($_SESSION['error'])) {
		/* Define some needed variables */
	    $password 	= User::encrypt_password($_POST['username'], $_POST['password']);
	    $active 	= '1';
		$type = '2';

		/* Add the user to the database */
		$stmt = $database->prepare("INSERT INTO `users` (`username`, `password`, `email`, `active`, `type`) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param('sssss', $_POST['username'], $password, $_POST['email'], $active, $type);
		$stmt->execute();
		$stmt->close();


		$_SESSION['success'][] = $language['messages']['success'];
	}

	display_notifications();

}


if(isset($_GET['status'])) {
	$user_data = new User($_GET['status']);

	/* Check for errors and permissions */
	if(!$token->is_valid()) {
		$_SESSION['error'][] = $language['errors']['invalid_token'];
	}
	if($_GET['status'] == $account_user_id) {
		$_SESSION['error'][] = $language['errors']['status_yourself'];
	}
	if(User::get_type($_GET['status']) > 0 && User::get_type($account_user_id) < 2) {
		$_SESSION['error'][] = $language['errors']['command_denied'];
	}

	if(empty($_SESSION['error'])) {
		if($user_data->active == true) $new_value = 0; else $new_value = 1;

		$database->query("UPDATE `users` SET `active` = {$new_value} WHERE `user_id` = {$_GET['status']}");
		$_SESSION['success'][] = $language['messages']['success'];
	}

	display_notifications();

}

if(isset($_GET['delete'])) {
	$user_data = new User($_GET['delete']);

	/* Check for errors and permissions */
	if(!$token->is_valid()) {
		$_SESSION['error'][] = $language['errors']['invalid_token'];
	}
	if($_GET['delete'] == $account_user_id) {
		$_SESSION['error'][] = $language['errors']['delete_yourself'];
	}
	if(User::get_type($account_user_id) < 2) {
		$_SESSION['error'][] = $language['errors']['command_denied'];
	}

	if(empty($_SESSION['error'])) {
		$database->query("DELETE FROM `users` WHERE `user_id` = {$_GET['delete']}");

		$_SESSION['success'][] = $language['messages']['success'];
	}

	display_notifications();

}



?>
<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="results">

		</tbody>
	</table>
</div>


<h4><?php echo $language['forms']['admin_add_user_header']; ?></h4>

<form action="" method="post" role="form">
	<div class="form-group">
		<label><?php echo $language['forms']['username']; ?></label>
		<input type="text" name="username" class="form-control" placeholder="<?php echo $language['forms']['username']; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['email']; ?></label>
		<input type="text" name="email" class="form-control" placeholder="<?php echo $language['forms']['email']; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['password']; ?></label>
		<input type="password" name="password" class="form-control" placeholder="<?php echo $language['forms']['password']; ?>" />
	</div>

	<div class="form-group">
		<label><?php echo $language['forms']['repeat_password']; ?></label>
		<input type="password" name="repeat_password" class="form-control" placeholder="<?php echo $language['forms']['repeat_password']; ?>" />
	</div>

	<div class="form-group">
		<button type="submit" name="submit" class="btn btn-primary"><?php echo $language['forms']['submit']; ?></button><br /><br />
	</div>

</form>
<script>
$(document).ready(function() {
	/* Load first answers */
	showMore(0, 'processing/admin_users_show_more.php', '#results', '#showMoreUsers');
});
</script>
