<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$h = Loader::helper('concrete/interface'); ?>

<?php  if ($this->controller->getTask() == 'set_developer') { ?>

<div id="ccm-module-row1">
<div class="ccm-module">

<form method="post" id="debug-form" action="<?php echo $this->url('/dashboard/settings', 'update_debug')?>">

<h1><span><?php echo t('Debug Level')?></span></h1>

<div class="ccm-dashboard-inner">
<p><?php echo t('Note: these are global settings. If enabled, PHP errors will be displayed to all visitors of the site.')?></p>

<div class="ccm-dashboard-radio"><input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_PRODUCTION?>" <?php  if ($debug_level == DEBUG_DISPLAY_PRODUCTION) { ?> checked <?php  } ?> /> <?php echo t('Production')?> </div>
<div class="ccm-dashboard-description"><?php echo t('PHP errors and database exceptions will be suppressed.')?></div>

<div class="ccm-dashboard-radio"><input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_ERRORS?>" <?php  if ($debug_level == DEBUG_DISPLAY_ERRORS) { ?> checked <?php  } ?> /> <?php echo t('Development')?> </div>
<div class="ccm-dashboard-description"><?php echo t('PHP errors and database exceptions will be displayed.')?></div>

<?php 
$b1 = $h->submit(t('Set Debug Level'), 'debug-form');
print $h->buttons($b1);
?>
<br class="clear" />
</div>

</form>

</div>
<div class="ccm-module">
<form method="post" id="logging-form" action="<?php echo $this->url('/dashboard/settings', 'update_logging')?>">
<h1><span><?php echo t('Logging')?></span></h1>
<div class="ccm-dashboard-inner">
<div class="ccm-dashboard-radio"><?php echo $form->checkbox('ENABLE_LOG_ERRORS', 1, $enable_log_errors)?> <?php echo t('Log Application Exceptions')?></div>
<div class="ccm-dashboard-description"><?php echo t('Saves application exceptions to logs.')?></div>
<div class="ccm-dashboard-radio"><?php echo $form->checkbox('ENABLE_LOG_DATABASE_QUERIES', 1, $enable_log_database_queries)?> <?php echo t('Log Database Activity')?></div>
<div class="ccm-dashboard-description"><?php echo t('Logs SQL queries for application profiling.')?></div>
<div class="ccm-dashboard-radio"><?php echo $form->checkbox('ENABLE_LOG_EMAILS', 1, $enable_log_emails)?> <?php echo t('Log Emails Sent')?></div>
<div class="ccm-dashboard-description">
	<?php echo t('Enables saving records of emails being sent out. This will save records even if actual email delivery is disabled on your site.')?>
</div>

<?php 
$b1 = $h->submit(t('Save Logging Settings'), 'logging-form');
print $h->buttons($b1);
?>


</div>
</form>
</div>
</div>

<?php  } else if ($this->controller->getTask() == 'set_permissions') { ?>

<h1><span><?php echo t('Site Permissions')?></span></h1>
<div class="ccm-dashboard-inner">


<?php  if (PERMISSIONS_MODEL != 'simple') { ?>

<p>
<?php echo t('Your Concrete site does not use the simple permissions model. You must change your permissions for each specific page and content area.')?>
</p>


<?php  } else { ?>

<form method="post" id="permissions-form" action="<?php echo $this->url('/dashboard/settings', 'update_permissions')?>">

<h2><?php echo t('Viewing Permissions')?></h2>


<div class="ccm-dashboard-radio"><input type="radio" name="view" value="ANYONE" style="vertical-align: middle" <?php  if ($guestCanRead) { ?> checked <?php  } ?> /> <?php echo t('Public')?></div>
<div class="ccm-dashboard-description"><?php echo t('Anyone may view the website.')?></div>

<div class="ccm-dashboard-radio"><input type="radio" name="view" value="USERS" style="vertical-align: middle" <?php  if ($registeredCanRead) { ?> checked <?php  } ?> /> <?php echo t('Members Only')?></div>
<div class="ccm-dashboard-description"><?php echo t('Only registered users may view the website.')?></div>

<div class="ccm-dashboard-radio"><input type="radio" name="view" value="PRIVATE" style="vertical-align: middle" <?php  if ((!$guestCanRead) && (!$registeredCanRead)) { ?> checked <?php  } ?> /> <?php echo t('Private')?></div>
<div class="ccm-dashboard-description"><?php echo t('Only the administrative group may view the website.')?></div>


<br/><br/>


<h2><?php echo t('Edit Access')?></h2>
<p>
<?php echo t('Choose which users and groups below may edit your site. Note: These settings can be overridden on specific pages.')?>
</p>

<?php 

foreach ($gArray as $g) {
?>

<input type="checkbox" name="gID[]" value="<?php echo $g->getGroupID()?>" <?php  if ($g->canWrite()) { ?> checked <?php  } ?> /> <?php echo $g->getGroupName()?><br/>

<?php  } ?>

<?php 
$b1 = $h->submit(t('Update Site Permissions'), 'permissions-form');
print $h->buttons($b1);
?>
<br class="clear" />
</form>

<?php  } ?>
</div>


<?php  } else { ?>



<div id="ccm-module-wrapper">
<div style="width: 778px">


<div class="ccm-module" style="width: 320px; margin-bottom: 0px">

<form method="post" id="site-form" action="<?php echo $this->url('/dashboard/settings', 'update_sitename')?>">

<h1><span><?php echo t('Site Name')?></span></h1>

<div class="ccm-dashboard-inner">

<div><?php echo $form->label('SITE', t('Name Your Website'))?></div>
<?php echo $form->text('SITE', $site, array('style'=>'width:285px'))?>

<?php 
$b1 = $h->submit(t('Save Site Name'), 'site-form');
print $h->buttons($b1);
?>
<br class="clear" />
</div>

</form>

<form method="post" id="url-form" action="<?php echo $this->url('/dashboard/settings', 'update_rewriting')?>">

<h1><span><?php echo t('Linking')?></span></h1>

<div class="ccm-dashboard-inner">

<div class="ccm-dashboard-radio"><?php echo $form->checkbox('URL_REWRITING', 1, $url_rewriting)?> <?php echo t('Enable Pretty URLs')?></div>
<div class="ccm-dashboard-description"><?php echo t("Automatically translates your path-based Concrete5 URLs so that they don't include 'index.php'.")?></div>

<?php  if (URL_REWRITING) { ?>
<h2><?php echo t('Required Code')?></h2>
<p><?php echo t("You must copy the lines of code below and place them in your server's configuration file or .htaccess file.")?></p>

<textarea style="width: 295px; height: 140px;" onclick="this.select()">
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase <?php echo DIR_REL?>/

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
</textarea>
<br/>
<br/>
<?php  } ?>

<?php 
$b1 = $h->submit(t('Save'), 'url-form');
print $h->buttons($b1);
?>
<br class="clear" />
</div>

</form>

</div>


<div class="ccm-module" style="width: 380px; margin-bottom: 0px">

<form method="post" id="user-settings-form" action="<?php echo $this->url('/dashboard/settings', 'update_user_settings')?>">

<h1><span><?php echo t('Editing Preferences')?></span></h1>

<div class="ccm-dashboard-inner">

<p><?php echo t('These editing preferences apply just to your user account.')?></p>

<div class="ccm-dashboard-radio">
<input type="checkbox" name="ui_breadcrumb" value="1"  <?php  if ($ui_breadcrumb == 1) { ?> checked <?php  } ?> /> 
<?php echo t('Display breadcrumb navigation bar.')?>
</div>
<div class="ccm-dashboard-description">
<?php echo t('When enabled, rolling your mouse over the editing bar will show the path to the current page.')?>
</div>

<?php 
$b1 = $h->submit(t('Save'), "user-settings-form");
print $h->buttons($b1);
?>
<br class="clear" />
</div>

</form>

<form method="post" id="maintenance-form" action="<?php echo $this->url('/dashboard/settings', 'update_maintenance')?>">

<h1><span><?php echo t('Maintenance Mode')?></span></h1>
<div class="ccm-dashboard-inner">

<p>
<?php echo t('Maintenance mode makes the front-end of the website inaccessible, while leaving the dashboard available to admin users.')?>
</p>

<div class="ccm-dashboard-radio"><input type="radio" name="site_maintenance_mode" value="0"  <?php  if ($site_maintenance_mode == 0) { ?> checked <?php  } ?> /> <?php echo t('Disabled')?></div>
<div class="ccm-dashboard-description"><?php echo t('When disabled, the site is available to the public.')?></div>

<div class="ccm-dashboard-radio"><input type="radio" id="site-maintenance-mode-enabled" name="site_maintenance_mode" value="1" <?php  if ($site_maintenance_mode == 1) { ?> checked <?php  } ?> /> <?php echo t('Enabled')?> </div>
<div class="ccm-dashboard-description"><?php echo t('If enabled, only your dashboard will be accessible.')?></div>

<?php 
$b1 = $h->button_js(t('Save'), 'saveMaintenanceMode');
print $h->buttons($b1);
?>
<br class="clear" />
</div>

</form>

</div>

</div>
</div>

<script type="text/javascript">
saveMaintenanceMode = function() {
	if ($('#site-maintenance-mode-enabled').get(0).checked) {
		if (confirm('<?php echo t('Are you sure you want to put your site into maintenance mode? This will make it inaccessible to public visitors.')?>')) {
			$("#maintenance-form").get(0).submit();
		}
	} else {
		$("#maintenance-form").get(0).submit();
	}
}
</script>

<?php  } ?>

<style type="Text/css">
div.ccm-dashboard-inner {margin-bottom: 10px !important}
</style>