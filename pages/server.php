<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */


include 'template/includes/modals/report.php';
include 'template/includes/modals/vote.php';
include 'core/functions/recaptchalib.php';

/* Check if server exists and the GET variables are not empty */
if(empty($_GET['address']) || empty($_GET['port']) || !$server->exists) {
	$_SESSION['error'][] = $language['errors']['server_not_found'];
} else {

	/* Check if server is disabled */
	if(!$server->data->active) {
		$_SESSION['error'][] = $language['errors']['server_not_active'];
	}

}

if(!empty($_SESSION['error'])) User::get_back();


/* Check if we should add another hit to the server or not */
$result = $database->query("SELECT `id` FROM `points` WHERE `type` = 0 AND `server_id` = {$server->data->server_id} AND `ip` = '{$_SERVER['REMOTE_ADDR']}' AND `timestamp` > UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY)");
if(!$result->num_rows) $database->query("INSERT INTO `points` (`type`, `server_id`, `ip`, `timestamp`) VALUES (0, {$server->data->server_id}, '{$_SERVER['REMOTE_ADDR']}', UNIX_TIMESTAMP())");


/* Check the cache timer, so we don't query the server
everytime we load the page */
if($server->data->cachetime > time() - $settings->cache_reset_time) {

	/* Decode the details content */
	$info = json_decode($server->data->details);

} else {

	$mcapi_url = 'https://mcapi.us/server/status?ip='.$server->data->query_address.'&port='.$server->data->query_port;
	$raw_data = file_get_contents($mcapi_url);
	$info = ($raw_data) ? json_decode($raw_data) : false;

	/* Update the cache depending on the  status */
	$info->online = (int) $info->online;
	if($info->online){
		$stmt = $database->prepare("UPDATE `servers` SET `status` = ?, `online_players` = ?, `maximum_online_players` = ?, `server_version` = ?, `details` = ?, `cachetime` = unix_timestamp() WHERE `server_id` = {$server->data->server_id}");
		$stmt->bind_param('sssss', $info->online, $info->players->now, $info->players->max, $info->server->name, $raw_data);
	} else {
		$stmt = $database->prepare("UPDATE `servers` SET `status` = ?, `details` = ?, `cachetime` = unix_timestamp() WHERE `server_id` = {$server->data->server_id}");
		$stmt->bind_param('ss', $info->online, $raw_data);
	}
	$stmt->execute();

	/* If the Uptime Tracking plugin is activated */
	if(Plugin::get('uptime', 'update.php')) include_once Plugin::get('uptime', 'update.php');

	/* Decode the MOTD */
	$info->motd = decodeMotd($info->motd);
}



?>

<h5>
	<?php echo $server->data->address . ":" . $server->data->connection_port; ?>
</h5>

<div id="response" style="display:none;"><?php output_success($language['messages']['success']); ?></div>


<table class="table">
	<tbody>
		<tr>
			<td style="width: 40%;"><i class="fa fa-clock-o" aria-hidden="true"></i> <strong><?php echo $language['server']['general_status']; ?></strong></td>
			<td>
				<?php
				if($info->online)
					echo '<span class="btn btn-outline-success btn-sm"><i class="fa fa-check"></i> ' . $language['server']['status_online'] . '</span>';
				else
					echo '<span class="btn btn-outline-danger btn-sm"><i class="fa fa-remove"></i> ' . $language['server']['status_offline'] . '</span>';
				?>
			</td>
		</tr>
		<tr>
			<td><i class="fa fa-random" aria-hidden="true"></i> <strong><?php echo $language['server']['general_address']; ?></strong></td>
			<td><?php echo $server->data->address ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-tasks" aria-hidden="true"></i> <strong><?php echo $language['server']['general_connection_port']; ?></strong></td>
			<td><?php echo $server->data->connection_port; ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-clock-o" aria-hidden="true"></i> <strong><?php echo $language['server']['general_last_check']; ?></strong></td>
			<td class="timeago" title="<?php if($server->data->cachetime > time() - $settings->cache_reset_time) echo @date("c", $server->data->cachetime); else echo date("c", time()); ?>"></td>
		</tr>
		<tr>
			<td><i class="fa fa-clock-o" aria-hidden="true"></i> <strong><?php echo $language['server']['general_previous_check']; ?></strong></td>
			<td class="timeago" title="<?php echo @date('c', $server->data->cachetime); ?>"></td>
		</tr>
		<tr>
			<td><i class="fa fa-cog" aria-hidden="true"></i> <strong><?php echo $language['server']['general_category']; ?></strong></td>
			<td><?php echo '<a href="category/' . $server->category->url . '">' . $server->category->name . '</a>'; ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-arrow-up" aria-hidden="true"></i> <strong><?php echo $language['server']['general_votes']; ?></strong></td>
			<td id="votes_value"><?php echo $server->data->votes; ?>  <a class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#vote"><span class="glyphicon glyphicon-stats"></span> <?php echo $language['server']['sidebar_vote']; ?></a></td>
		</tr>
		<tr>
			<td><i class="fa fa-users" aria-hidden="true"></i> <strong><?php echo $language['server']['general_hits']; ?></strong></td>
			<td><?php echo $server->hits; ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-globe" aria-hidden="true"></i> <strong><?php echo $language['server']['general_country']; ?></strong></td>
			<td><?php echo country_check(2, $server->data->country_code); ?></td>
		</tr>
		<?php if(!empty($server->data->website)) { ?>
		<tr>
			<td><i class="fa fa-link" aria-hidden="true"></i> <strong><?php echo $language['forms']['server_website']; ?></strong></td>
			<td><a href="<?php echo $server->data->website; ?>"><?php echo $server->data->website; ?></a></td>
		</tr>
		<?php } ?>

		<tr>
			<td><i class="fa fa-users" aria-hidden="true"></i> <strong><?php echo $language['server']['general_online_players']; ?></strong></td>
			<td><?php echo $info->players->now; ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-users" aria-hidden="true"></i> <strong><?php echo $language['server']['general_maximum_online_players']; ?></strong></td>
			<td><?php echo $info->players->max; ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-task" aria-hidden="true"></i> <strong><?php echo $language['server']['motd']; ?></strong></td>
			<td><?php echo $info->motd; ?></td>
		</tr>
		<tr>
			<td><i class="fa fa-wrench" aria-hidden="true"></i> <strong><?php echo $language['server']['server_version']; ?></strong></td>
			<td><?php echo $info->server->name; ?></td>
		</tr>

		<tr>
			<td>
				<a class="btn btn btn-outline-warning btn-sm" onclick="report(<?php echo $server->data->server_id; ?>, 2);">
					<span class="glyphicon glyphicon-exclamation-sign"></span> <?php echo $language['misc']['report']; ?>
				</a>
			</td>
			<td>
				<a href="admin/edit-server/<?php echo $server->data->server_id; ?>" class="btn btn-default">
					<span class="glyphicon glyphicon-pencil"></span> <?php echo $language['forms']['server_admin_edit']; ?>
				</a>
			</td>
		</tr>
	</tbody>
</table>


<!-- Description -->
<?php if(!empty($server->data->description)) { ?>
	<br />
	<h5>
		<?php echo $language['server']['description']; ?>
	</h5>

	<?php echo bbcode($server->data->description); ?>
<?php } ?>

<!-- Video -->
<?php if(!empty($server->data->youtube_id)) { ?>
	<br />
	<h5>
		<?php echo $language['server']['video']; ?>
	</h5>

	<div class="video-container">
		<?php echo youtube_convert($server->data->youtube_id); ?>
	</div>
<?php } ?>

<br />

<!-- Recaptcha base -->
<div id="recaptcha_base">
	<div id="recaptcha" style="display:none;"><?php echo recaptcha_get_html($settings->public_key); ?></div>
</div>
