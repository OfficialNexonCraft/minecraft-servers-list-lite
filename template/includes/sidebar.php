<?php

include 'widgets/categories.php';

include_once Plugin::get('latest-servers', 'widget.php');

$admin_pages = array('admin_website_settings', 'admin_website_statistics', 'admin_users_management', 'admin_user_edit', 'admin_servers_management', 'admin_server_edit', 'admin_reset', 'admin_reports_management', 'admin_report_edit', 'admin_category_edit', 'admin_categories_management');
if(isset($_GET['page']) && in_array($_GET['page'], $admin_pages)): ?>
<a class="twitter-timeline"  href="https://twitter.com/grohsfabian" data-widget-id="365893913531269120">Tweets by @grohsfabian</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<?php endif;


if(!empty($settings->side_ads)) echo $settings->side_ads;
?>
