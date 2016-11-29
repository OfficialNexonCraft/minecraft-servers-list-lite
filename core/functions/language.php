<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
$path 		 = ROOT . '/languages/';    		//languages directory
$defLanguage = 'english';    					//default language
$lang 		 = $defLanguage; 					//current language variable
$languages   = array();        					//store the available languages

$files = glob($path . '*.php');
foreach($files as $key => $value) $files[$key] = @end(explode('/', $value));
$languages = str_replace('.php', '', $files);

/* If cookie is set and its correct, then override the default language */
if(isset($_COOKIE['language']) && in_array($_COOKIE['language'], $languages)) $lang = $_COOKIE['language'];

/* Check for get request to change the current language */
if(isset($_GET['language'])) {
	$_GET['language'] = filter_var($_GET['language'], FILTER_SANITIZE_STRING);
	if(in_array($_GET['language'], $languages)) {
		setcookie('language', $_GET['language'], time()+60*60*24*3); //set cookie for 30 days with that specific language
		$lang = $_GET['language'];
	}
}

/* Include the language file */
if(file_exists($path . $lang . '.php'))	include $path . $lang . '.php';

?>
