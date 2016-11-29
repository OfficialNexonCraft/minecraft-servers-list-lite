<div class="header clearfix">
	<nav>
		<ul class="nav nav-pills float-xs-right">
			<li class="nav-item">
				<a class="nav-link" href="submit"><i class="fa fa-plus"></i> <?php echo $language['menu']['submit']; ?></a>
			</li>

			<?php if(User::logged_in()): ?>
			<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $language['menu']['admin']; ?></a>
			<div class="dropdown-menu">
				<a class="dropdown-item" href="settings/password"><?php echo $language['menu']['change_password']; ?></a>
				<a class="dropdown-item" href="admin/users-management"><?php echo $language['menu']['users_management']; ?></a>
				<a class="dropdown-item" href="admin/servers-management"><?php echo $language['menu']['servers_management']; ?></a>
				<a class="dropdown-item" href="admin/categories-management"><?php echo $language['menu']['categories_management']; ?></a>
				<a class="dropdown-item" href="admin/reports-management"><?php echo $language['menu']['reports_management']; ?></a>
				<?php if(User::get_type($account_user_id) > 1) { ?>
				<a class="dropdown-item" href="admin/website-settings"><?php echo $language['menu']['website_settings']; ?></a>
				<a class="dropdown-item" data-confirm="<?php echo $language['messages']['reset_votes']; ?>" href="admin/reset"><?php echo $language['menu']['reset_votes']; ?></a>
				<?php } ?>
				<a class="dropdown-item" href="admin/website-statistics"><?php echo $language['menu']['website_statistics']; ?></a>

			</div>
			</li>
			<?php endif;?>
		</ul>
	</nav>
	<h3 class="title"><a href="<?php echo $settings->url; ?>"><?php echo $settings->title; ?></a></h4>
</div>
