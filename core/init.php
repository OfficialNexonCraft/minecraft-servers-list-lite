<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */

ob_start();
session_start();
error_reporting(E_ALL);

define('ROOT', realpath(__DIR__ . '/..'));

include 'database/connect.php';
include 'functions/language.php';
include 'functions/general.php';
include 'classes/User.php';
include 'classes/Pagination.php';
include 'classes/Server.php';
include 'classes/Servers.php';
include 'classes/Csrf.php';
include 'classes/Plugin.php';
include 'classes/PHPMailer/PHPMailerAutoload.php';

/* Plugins System */
Plugin::init();
foreach(Plugin::$auto_files as $file) include $file;

/* Initialize variables */
$errors 	= array();
$settings 	= settings_data();
$token 		= new CsrfProtection();


/* Set the default timezone if its not set in the ini file */
$date_timezone = ini_get('date.timezone');
if(empty($date_timezone))
	date_default_timezone_set('UTC');

/* If user is logged in get his data */
if(User::logged_in()) {
	$account_user_id = (isset($_SESSION['user_id']) == true) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
	$account = new User($account_user_id);

	/* Update last activity */
	$database->query("UPDATE `users` SET `last_activity` = unix_timestamp() WHERE `user_id` = {$account_user_id}");
}


/* Get server data if needed */
if(!empty($_GET['address']) && !empty($_GET['port']) && $_GET['page'] == 'server') {
	$server = new Server($_GET['address'], $_GET['port']);
	if($server->exists) $_SESSION['server_id'] = $server->data->server_id;
	$server->data->query_address = $server->data->address;
}

/* If the page is category do: */
if(!empty($_GET['url']) && $_GET['page'] == 'category') {

	/* Get $category data from the database */
	$stmt = $database->prepare("SELECT * FROM `categories` WHERE BINARY `url` = ?");
	$stmt->bind_param('s', $_GET['url']);
	$stmt->execute();
	bind_object($stmt, $category);
	$stmt->fetch();
	$stmt->close();

	$category_exists = ($category !== NULL);
}


include 'functions/titles.php';

?>
