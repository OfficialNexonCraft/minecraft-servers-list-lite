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
				<th>Status</th>
				<th>IP Address</th>
				<th>Port</th>
				<th>Category</th>
				<th>Edit Server</th>
			</tr>
		</thead>
		<tbody id="results">

		</tbody>
	</table>
</div>

<script>
$(document).ready(function() {
	/* Load first answers */
	showMore(0, 'processing/admin_servers_show_more.php', '#results', '#showMoreServers');
});
</script>
