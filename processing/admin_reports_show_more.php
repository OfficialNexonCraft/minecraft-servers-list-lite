<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
include '../core/init.php';

$_POST['limit'] = (int) $_POST['limit'];
$results_limit = 25;

$result = $database->query("SELECT * FROM `reports` ORDER BY `id` ASC LIMIT {$_POST['limit']}, {$results_limit}");
while($reports_data = $result->fetch_object()) {
?>
<tr>
	<td><?php echo $reports_data->reported_id; ?></td>
	<td><?php echo $reports_data->date; ?></td>
	<td><a href="admin/edit-report/<?php echo $reports_data->id; ?>">View report</a></td>
</tr>
<?php } ?>

<?php if($result->num_rows == $results_limit) { ?>
<tr id="showMoreReports">
	<td colspan="6">
		<div class="center">
			<button id="showMore" class="btn btn-default" onClick="showMore(<?php echo $_POST['limit'] + $results_limit; ?>, 'processing/admin_users_show_more.php', '#results', '#showMoreReports');"><?php echo $language['misc']['show_more']; ?></button>
		</div>
	</td>
</tr>
<?php } ?>
