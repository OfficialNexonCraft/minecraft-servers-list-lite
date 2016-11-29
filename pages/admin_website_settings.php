<?php
/*
	This script was created by Grohs Fabian
	The official link for this script is: https://github.com/grohsfabian/minecraft-servers-list-lite
	Check out my Twitter @grohsfabian | My website http://grohsfabian.com
	Portfolio: http://codecanyon.net/user/grohsfabian
 */
 
User::check_permission(2);

if(!empty($_POST)) {
	/* Define some variables */
	$_POST['title']				 		= filter_var($_POST['title'], FILTER_SANITIZE_STRING);
	$_POST['meta_description']	 		= filter_var($_POST['meta_description'], FILTER_SANITIZE_STRING);
	$_POST['analytics_code']	 		= filter_var($_POST['analytics_code'], FILTER_SANITIZE_STRING);
	$_POST['public_key']				= filter_var($_POST['public_key'], FILTER_SANITIZE_STRING);
	$_POST['private_key']				= filter_var($_POST['private_key'], FILTER_SANITIZE_STRING);
	$_POST['contact_email']				= filter_var($_POST['contact_email'], FILTER_SANITIZE_STRING);
	$_POST['servers_pagination']		= (int)$_POST['servers_pagination'];
	$_POST['cache_reset_time']			= (int)$_POST['cache_reset_time'];
	$_POST['display_offline_servers'] 	= (isset($_POST['display_offline_servers'])) ? 1 : 0;

	/* Prepare the statement and execute query */
	$stmt = $database->prepare("UPDATE `settings` SET `title` = ?, `meta_description` = ?, `analytics_code` = ?, `servers_pagination` = ?, `contact_email` = ?, `cache_reset_time` = ?, `display_offline_servers` = ?, `top_ads` = ?, `bottom_ads` = ?, `side_ads` = ?, `public_key` = ?, `private_key` = ?  WHERE `id` = 1");
	$stmt->bind_param(
		'ssssssssssss',
		$_POST['title'],
		$_POST['meta_description'],
		$_POST['analytics_code'],
		$_POST['servers_pagination'],
		$_POST['contact_email'],
		$_POST['cache_reset_time'],
		$_POST['display_offline_servers'],
		$_POST['top_ads'],
		$_POST['bottom_ads'],
		$_POST['side_ads'],
		$_POST['public_key'],
		$_POST['private_key']
	);
	$stmt->execute();
	$stmt->close();

	/* Set message & Redirect */
	$_SESSION['success'][] = $language['messages']['settings_updated'];
	redirect("admin/website-settings");

}



?>
<h4><?php echo $language['headers']['website_settings']; ?></h4>


<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a href="#main" data-toggle="tab" role="tab" class="nav-link active"><?php echo $language['forms']['main_settings']; ?></a></li>
	<li class="nav-item"><a href="#servers" data-toggle="tab" role="tab" class="nav-link"><?php echo $language['forms']['servers_settings']; ?></a></li>
	<li class="nav-item"><a href="#ads" data-toggle="tab" role="tab" class="nav-link"><?php echo $language['forms']['ads_settings']; ?></a></li>
	<li class="nav-item"><a href="#recaptcha" data-toggle="tab" role="tab" class="nav-link"><?php echo $language['forms']['recaptcha_settings']; ?></a></li>
</ul>


<form action="" method="post" role="form">
	<div class="tab-content">
 		<div class="tab-pane fade in active" id="main">
			<div class="form-group">
				<label><?php echo $language['forms']['settings_title']; ?></label>
				<input type="text" name="title" class="form-control" value="<?php echo $settings->title; ?>" />
			</div>

			<div class="form-group">
				<label><?php echo $language['forms']['settings_meta_description']; ?></label>
				<input type="text" name="meta_description" class="form-control" value="<?php echo $settings->meta_description; ?>" />
			</div>

			<div class="form-group">
				<label><?php echo $language['forms']['settings_analytics_code']; ?></label>
				<p class="help-block"><?php echo $language['forms']['settings_analytics_code_help']; ?></p>
				<input type="text" name="analytics_code" class="form-control" value="<?php echo $settings->analytics_code; ?>" />
			</div>


			<div class="form-group">
				<label><?php echo $language['forms']['settings_contact_email']; ?></label>
				<p class="help-block"><?php echo $language['forms']['settings_contact_email_help']; ?></p>
				<input type="text" name="contact_email" class="form-control" value="<?php echo $settings->contact_email; ?>" />
			</div>


		</div>

		<div class="tab-pane fade" id="servers">
			<div class="form-group">
				<label><?php echo $language['forms']['settings_cache_reset_time']; ?></label>
				<p class="help-block"><?php echo $language['forms']['settings_cache_reset_time_help']; ?></p>
				<input type="text" name="cache_reset_time" class="form-control" value="<?php echo $settings->cache_reset_time; ?>" />
			</div>

			<div class="form-group">
				<label><?php echo $language['forms']['settings_servers_pagination']; ?></label>
				<p class="help-block"><?php echo $language['forms']['settings_servers_pagination_help']; ?></p>
				<input type="text" name="servers_pagination" class="form-control" value="<?php echo $settings->servers_pagination; ?>" />
			</div>

			<div class="checkbox">
				<label>
					<?php echo $language['forms']['settings_display_offline_servers']; ?><input type="checkbox" name="display_offline_servers" <?php if($settings->display_offline_servers) echo 'checked'; ?>>
				</label>
			</div>
		</div>

		<div class="tab-pane fade" id="ads">
			<div class="form-group">
				<label><?php echo $language['forms']['settings_top_ads']; ?></label>
				<textarea class="form-control" name="top_ads"><?php echo $settings->top_ads; ?></textarea>
			</div>

			<div class="form-group">
				<label><?php echo $language['forms']['settings_bottom_ads']; ?></label>
				<textarea class="form-control" name="bottom_ads"><?php echo $settings->bottom_ads; ?></textarea>
			</div>

			<div class="form-group">
				<label><?php echo $language['forms']['settings_side_ads']; ?></label>
				<textarea class="form-control" name="side_ads"><?php echo $settings->side_ads; ?></textarea>
			</div>
		</div>

		<div class="tab-pane fade" id="recaptcha">
			<div class="form-group">
				<label><?php echo $language['forms']['settings_public_key']; ?></label>
				<input type="text" name="public_key" class="form-control" value="<?php echo $settings->public_key; ?>" />
			</div>

			<div class="form-group">
				<label><?php echo $language['forms']['settings_private_key']; ?></label>
				<input type="text" name="private_key" class="form-control" value="<?php echo $settings->private_key; ?>" />
			</div>
		</div>


		<div class="form-group">
			<button type="submit" name="submit" class="btn btn-primary"><?php echo $language['forms']['submit']; ?></button><br /><br />
		</div>
	</div>
</form>

<h4>Status</h4>
<p><?php echo @file_get_contents('http://grohsfabian.com/phpminecraft-lite.php'); ?></p>
