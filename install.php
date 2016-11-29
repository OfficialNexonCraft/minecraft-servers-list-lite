<?php
error_reporting(1);
$errors = array();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Installation</title>
		    <meta charset="UTF-8">
			<link href="template/css/bootstrap.min.css" rel="stylesheet" media="screen">
			<script src="template/js/jquery.js"></script>
		    <script src="template/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<h2>Welcome !</h2>

			<div class="panel panel-default">
				<div class="panel-body">
					<?php
					if(!empty($_POST)) {
						/* Define some variables */
						$database_server 	= $_POST['database_server'];
						$database_user	 	= $_POST['database_user'];
						$database_password  = $_POST['database_password'];
						$database_name		= $_POST['database_name'];

						$database = new mysqli($database_server, $database_user, $database_password, $database_name);
						$connect_file = "core/database/connect.php";

						/* Check for any errors */
						if(!function_exists('mysqli_connect')) {
							$errors[] = "Please make sure you have the MySQLi extension installed and enabled !";
						}
						if($database->connect_error) {
							$errors[] = 'We couldn\'t connect to the database !';
						}
						if(!is_readable($connect_file) || !is_writable($connect_file)) {
							$errors[] = '<u><strong>core/database/connect.php</strong></u> doesn\'t have CHMOD 777';
						}
						if(filter_var($_POST['settings_url'], FILTER_VALIDATE_URL) == false) {
							$errors[] = 'Your website url is not valid !';
						}

						if(empty($errors)) {
							/* add "/" if the user didnt added it */
							if(substr($_POST['settings_url'], -1) !== "/") {
								$_POST['settings_url'] .= "/";
							}

							/* Define the connect.php content */
							$connect_content = <<<PHP
<?php
// Connection parameters
\$DatabaseServer = "$database_server";
\$DatabaseUser   = "$database_user";
\$DatabasePass   = "$database_password";
\$DatabaseName   = "$database_name";

// Connecting to the database
\$database = new mysqli(\$DatabaseServer, \$DatabaseUser, \$DatabasePass, \$DatabaseName);

?>
PHP;
							/* open, write and close */
							$command = fopen($connect_file, w);
							fwrite($command, $connect_content);
							fclose($command);

							/* Add the tables to the database */
							$database->query("
								CREATE TABLE `categories` (
								  `category_id` int(11) NOT NULL AUTO_INCREMENT,
								  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
								  `description` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
								  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
								  `url` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
								  PRIMARY KEY (`category_id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
							");

							$database->query("
								CREATE TABLE `points` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `type` int(11) NOT NULL,
								  `server_id` int(11) NOT NULL,
								  `ip` varchar(32) NOT NULL,
								  `timestamp` int(11) NOT NULL,
								  PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
							");

							$database->query("
								CREATE TABLE `reports` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `ip_address` varchar(64) NOT NULL DEFAULT '',
								  `type` int(11) NOT NULL,
								  `reported_id` int(11) NOT NULL,
								  `message` varchar(512) NOT NULL,
								  `date` varchar(32) NOT NULL,
								  PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
							");

							$database->query("
								CREATE TABLE `servers` (
								  `server_id` int(11) NOT NULL AUTO_INCREMENT,
								  `user_id` int(11) NOT NULL,
								  `category_id` int(11) NOT NULL,
								  `address` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
								  `connection_port` int(11) NOT NULL,
								  `query_port` int(11) NOT NULL,
								  `active` int(11) NOT NULL DEFAULT '0',
								  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
								  `description` varchar(2560) COLLATE utf8_unicode_ci NOT NULL,
								  `country_code` varchar(2) COLLATE utf8_unicode_ci DEFAULT '',
								  `youtube_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
								  `date_added` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
								  `highlight` int(11) NOT NULL DEFAULT '0',
								  `votes` int(11) NOT NULL DEFAULT '0',
								  `status` int(11) NOT NULL DEFAULT '0',
								  `online_players` int(11) NOT NULL DEFAULT '0',
								  `maximum_online_players` int(11) NOT NULL DEFAULT '0',
								  `motd` varchar(256) COLLATE utf8_unicode_ci DEFAULT '',
								  `server_version` varchar(256) COLLATE utf8_unicode_ci DEFAULT '',
								  `details` longtext COLLATE utf8_unicode_ci,
								  `cachetime` varchar(16) COLLATE utf8_unicode_ci DEFAULT '',
								  PRIMARY KEY (`server_id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
							");
							$database->query("
								CREATE TABLE `settings` (
								  `id` int(11) NOT NULL,
								  `title` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `url` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `meta_description` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `analytics_code` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `servers_pagination` int(11) DEFAULT '10',
								  `contact_email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `cache_reset_time` int(11) DEFAULT NULL,
								  `display_offline_servers` int(11) DEFAULT '1',
								  `top_ads` varchar(2560) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `bottom_ads` varchar(2560) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `side_ads` varchar(2560) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `public_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
								  `private_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
								  PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
							");
							$database->query("
								CREATE TABLE `users` (
								  `user_id` int(11) NOT NULL AUTO_INCREMENT,
								  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
								  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
								  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
								  `lost_password_code` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
								  `type` int(11) NOT NULL DEFAULT '0',
								  `active` int(11) NOT NULL DEFAULT '0',
								  `last_activity` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
								  PRIMARY KEY (`user_id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
							");

							$database->query("
								INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `lost_password_code`, `type`, `active`, `last_activity`)
								VALUES
									(1,'admin','365a4a0e748d76932d03cd46e62e4c3b4ca426c00c87bdf6ca9e692a0dc797224d151c3c9156a57c624e5bef533f0af9b8059726987c7929281a6b7acf7af8d4','admin@admin.com','0',2,1,'1480418142'),
							");
							$database->query("
								INSERT INTO `settings` (`id`, `title`, `url`, `meta_description`, `analytics_code`, `servers_pagination`, `contact_email`, `cache_reset_time`, `display_offline_servers`, `top_ads`, `bottom_ads`, `side_ads`, `public_key`, `private_key`)
								VALUES (1, '" . $_POST['settings_title'] . "', '" . $_POST['settings_url'] . "','','',5,'no-reply@domain.com',600,1,'','','','6Le43tISAAAAADni-XsMzvEaStTluh6vSFmbhpfC','6Le43tISAAAAANP9dDZb-ConEQRFxdyTpNFo09Q3');
							");

							$database->query("
								INSERT INTO `categories` (`category_id`, `parent_id`, `name`, `description`, `title`, `url`, `image`)
								VALUES (1,0,'Minecraft','','Minecraft','minecraft','');
							");

							/* Display a success message */
							$_SESSION['success'][] = 'You can now login with the admin account ( admin / admin )';
							$_SESSION['info'][]	= 'Make sure to delete the install.php file !';
							header('Location: ' . $_POST['settings_url']);

						} else {

							/* Display all the errors if needed */
							foreach($errors as $nr => $error) {
								echo '<div class="alert alert-warning">' . $error . '</div>';
							}

							echo '<a href="install.php"><button class="btn btn-primary">Go back !</button></a>';
						}
					} else {
					?>
					<div class="alert alert-info">Make sure the <u><strong>core/database/connect.php</strong></u> file has CHMOD 777 before installing !</div>

					<form action="" method="post" role="form">
						<div class="form-group">
							<label>Database Server</label>
							<input type="text" class="form-control" name="database_server" value="localhost" />
						</div>
						<div class="form-group">
							<label>Database User</label>
							<input type="text" class="form-control" name="database_user" />
						</div>
						<div class="form-group">
							<label>Database Password</label>
							<input type="text" class="form-control" name="database_password" />
						</div>
						<div class="form-group">
							<label>Database Name</label>
							<input type="text" class="form-control" name="database_name" />
						</div>

						<div class="form-group">
							<label>URL</label>
							<p class="help-block">e.g: http://domain.com/directory/</p>
							<input type="text" class="form-control" name="settings_url" />
						</div>
						<div class="form-group">
							<label>Site Title</label>
							<input type="text" class="form-control" name="settings_title" />
						</div>

						<div class="form-group">
							<button type="submit" name="submit" class="btn btn-primary col-lg-4">Install</button>
						</div>
					</form>
					<?php } ?>
				</div>

				<div class="panel-footer">
					<span>Created by <a href="http://twitter.com/grohsfabian">Grohs Fabian</a></span>
				</div>

			</div>

		</div>
	</body>
</html>
