<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */

class Servers {
	private $order_by;
	private $additional_join = null;
	private $where;

	public $pagination;
	public $server_results;
	public $affix;
	public $country_options;
	public $version_options;
	public $no_servers;

	public function __construct($category_id = false) {
		global $database;
		global $settings;
		global $language;

		/* Initiate the affix and start generating it */
		$this->affix = '';

		/* Order */
		$this->order_by = 'ORDER BY `servers`.`highlight` DESC, `servers`.`votes` DESC';

		/* Filtering system */
		$category_where = ($category_id !== false) ? 'AND `servers`.`category_id` = ' . (int) $category_id : null;

		/* The default status filtering ( when there are no status filter active ) */
		$default_status_where = (!$settings->display_offline_servers) ? 'AND `servers` . `status` = \'1\'' : null;

		/* If affix isn't empty prepend the ? sign so it can be processed */
		$this->affix = (!empty($this->affix)) ? '?' . $this->affix : null;

		/* Create the maine $where variable */
		$this->where = "WHERE 1=1 {$category_where} {$default_status_where}";

		/* Generate pagination */
		$this->pagination = new Pagination($settings->servers_pagination, $this->where);

		/* Set the default no servers message */
		$this->no_servers = $language['messages']['no_servers'];

	}

	public function additional_where($where) {
		global $settings;

		/* Remake the where with the additional condition */
		$this->where = $this->where . ' ' . $where;

		/* Remake the pagination */
		$this->pagination = new Pagination($settings->servers_pagination, $this->where);

	}

	public function additional_join($join) {
		global $settings;

		/* This is mainly so we can gather the data based on the favorite servers */
		$this->additional_join = $join;

		/* Remake the pagination with the true condition so it counts the servers correctly */
		$this->pagination = new Pagination($settings->servers_pagination, $this->where, true);

	}

	public function remove_pagination() {

		/* Make the pagination null */
		$this->pagination->limit = null;

	}

	public function display() {
		global $database;
		global $language;
		global $account_user_id;

		/* Quickly verify the remaining of highlighted days remaining */
		$database->query("UPDATE `servers` JOIN `payments` ON `servers`.`server_id` = `payments`.`server_id` SET `servers`.`highlight` = '0' WHERE `payments`.`date` + INTERVAL `payments`.`highlighted_days` DAY < CURDATE()");

		if(Plugin::get('bidding-system', 'update-servers.php')) include_once Plugin::get('bidding-system', 'update-servers.php');

		/* Retrieve servers information */
		$result = $database->query("SELECT * FROM `servers` {$this->additional_join} {$this->where} {$this->order_by} {$this->pagination->limit}");

		/* Check if there is any result */
		$this->server_results = $result->num_rows;
		if($this->server_results < 1) $_SESSION['info'][] = $this->no_servers;

		/* Display the servers */
		while($server = $result->fetch_object()) {

		/* Get category information for the servers */
		$category_result = $database->query("SELECT `name`, `url` FROM `categories` WHERE `category_id` = {$server->category_id}");
		$category = $category_result->fetch_object();

		/* Store the status into a variable */
		$server->status_text = ($server->status) ? $language['server']['status_online'] : $language['server']['status_offline'];

		/* Check if there is any image uploaded, if not, display default */
		$server->image = (empty($server->image)) ? 'default.jpg' : $server->image;

		?>

		<div class="card card-block <?php if($server->highlight) echo 'card-highlighted'; ?>">
			<div class="row">
				<div class="col-md-8">
					<p class="card-text no-margin"><a href="server/<?php echo $server->address . ':' . $server->connection_port; ?>"><?php echo $server->name; ?></a></p>
					<p class="small no-margin no-padding"><?php echo $server->address . ($server->connection_port != '25565' ? ':' . $server->connection_port : null); ?></p>
				</div>

				<div class="col-md-2">
					<span class="glyphicon glyphicon-user"></span><?php echo $server->online_players . '/' . $server->maximum_online_players; ?>
				</div>

				<div class="col-md-2">
					<span class="label-<?php echo strtolower($server->status_text); ?>"><?php echo $server->status_text; ?></span>
				</div>
			</div>
		</div>

		<?php }
	}

	public function display_pagination($current_page) {

		/* If there are results, display pagination */
		if($this->server_results > 0) {

			/* Establish the current page link */
			$this->pagination->set_current_page_link($current_page);

			/* Display */
			$this->pagination->display($this->affix);
		}
	}

}
?>
