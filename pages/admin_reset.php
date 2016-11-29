<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
User::check_permission(2);

/* Delete and reset the vote & hit logs */
$database->query("DELETE FROM `points`");
$database->query("UPDATE `servers` SET `votes` = '0'");

/* Set the success message & redirect*/
$_SESSION['success'][] = $language['messages']['success'];
User::get_back('index');

?>
