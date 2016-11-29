<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */


User::check_permission(1);
?>

<div class="row">
	<div class="col-md-6">

		<?php
		$result = $database->query("
			SELECT
				(SELECT COUNT(*) FROM `categories`) AS `categories_count`,
				(SELECT COUNT(*) FROM `reports`) AS `reports_count`,
				(SELECT COUNT(*) FROM `servers`) AS `servers_count`,
				(SELECT COUNT(*) FROM `users`) AS `users_count`
			");
		$total_data = $result->fetch_object();
		?>

		<h4><?php echo $language['misc']['total_statistics']; ?></h4>

		<table class="table-fixed-full table-statistics">
			<tr>
				<td style="width:50%"><?php echo $language['misc']['statistics_categories']; ?></td>
				<td style="width:50%"><kbd><?php echo $total_data->categories_count; ?></kbd></td>
			</tr>
			<tr>
				<td style="width:50%"><?php echo $language['misc']['statistics_reports']; ?></td>
				<td style="width:50%"><kbd><?php echo $total_data->reports_count; ?></kbd></td>
			</tr>
			<tr>
				<td style="width:50%"><?php echo $language['misc']['statistics_servers']; ?></td>
				<td style="width:50%"><kbd><?php echo $total_data->servers_count; ?></kbd></td>
			</tr>
			<tr>
				<td style="width:50%"><?php echo $language['misc']['statistics_users']; ?></td>
				<td style="width:50%"><kbd><?php echo $total_data->users_count; ?></kbd></td>
			</tr>
		</table>

	</div>

	<div class="col-md-6">

		<?php
		$result = $database->query("
			SELECT
				(SELECT COUNT(`server_id`) AS `count` FROM `servers` WHERE YEAR(`date_added`) = YEAR(CURDATE()) AND MONTH(`date_added`) = MONTH(CURDATE()) AND DAY(`date_added`) = DAY(CURDATE())) AS `new_servers_today`,
				(SELECT COUNT(`server_id`) AS `count` FROM `servers` WHERE `cachetime` > UNIX_TIMESTAMP() - 86400) AS `active_servers_today`,
				(SELECT COUNT(`server_id`) AS `count` FROM `servers` WHERE `status` = '1') AS `online_servers`,
				(SELECT COUNT(`server_id`) AS `count` FROM `servers` WHERE `status` = '0') AS `offline_servers`,
				(SELECT COUNT(`server_id`) AS `count` FROM `servers` WHERE `active` = '1') AS `active_servers`
			");
		$servers_data = $result->fetch_object();
		?>

		<h4><?php echo $language['misc']['servers_statistics']; ?></h4>

		<table class="table-fixed-full table-statistics">
			<tr>
				<td style="width:50%"><?php echo $language['misc']['new_servers_today']; ?></td>
				<td style="width:50%"><kbd><?php echo $servers_data->new_servers_today; ?></kbd></td>
			</tr>

			<tr>
				<td style="width:50%"><?php echo $language['misc']['active_servers_today']; ?></td>
				<td style="width:50%"><kbd><?php echo $servers_data->active_servers_today; ?></kbd></td>
			</tr>

			<tr>
				<td style="width:50%"><?php echo $language['misc']['online_servers']; ?></td>
				<td style="width:50%"><kbd><?php echo $servers_data->online_servers; ?></kbd></td>
			</tr>

			<tr>
				<td style="width:50%"><?php echo $language['misc']['offline_servers']; ?></td>
				<td style="width:50%"><kbd><?php echo $servers_data->offline_servers; ?></kbd></td>
			</tr>

			<tr>
				<td style="width:50%"><?php echo $language['misc']['active_servers']; ?></td>
				<td style="width:50%"><kbd><?php echo $servers_data->active_servers; ?></kbd></td>
			</tr>
		</table>

	</div>
</div>
