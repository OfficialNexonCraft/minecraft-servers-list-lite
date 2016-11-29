<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */


User::check_permission(1);
?>

<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Reported Id</th>
				<th>Date</th>
				<th>View report</th>
			</tr>
		</thead>
		<tbody id="results">

		</tbody>
	</table>
</div>

<script>
$(document).ready(function() {
	/* Load first answers */
	showMore(0, 'processing/admin_reports_show_more.php', '#results', '#showMoreReports');
});
</script>
