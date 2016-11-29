<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
class Plugin {

	static public $plugins = array();
	static public $auto_files = array();

	public static function init() {

		/* Get all the directories from the plugins folder */
		$directories = array_filter(glob(ROOT . '/plugins/*'), 'is_dir');

		/* Search through them and verify them */
		foreach($directories as $directory) {

			if(file_exists($directory . '/config.php') && $plugin = parse_ini_file($directory . '/config.php')) {

				$plugin_name = @end(explode('/', $directory));
				self::$plugins[$plugin_name] = $plugin;

				/* If the auto folder exists, iterate through it */
				if(is_dir($directory . '/files/auto')) {
					$auto_files = array_diff(scandir($directory . '/files/auto/'), array('..', '.'));

					foreach($auto_files as $file) self::$auto_files[] = $directory . '/files/auto/' . $file;
				}
			}

		}

		return self::$plugins;
	}

	public static function get($plugin_name, $plugin_file) {

		/* Check if the plugin is active and exists first */
		return (self::active($plugin_name) && file_exists(ROOT . '/plugins/' . $plugin_name . '/files/' . $plugin_file)) ?
			ROOT . '/plugins/' . $plugin_name . '/files/' . $plugin_file : false;

	}


	public static function active($plugin_name) {
		return (self::exists($plugin_name) && self::$plugins[$plugin_name]['active']);
	}

	public static function exists($plugin_name) {
		return (@is_array(self::$plugins[$plugin_name]));
	}

	public static function access_check($plugin_name) {
		if(!self::active($plugin_name)) redirect();
	}

}
