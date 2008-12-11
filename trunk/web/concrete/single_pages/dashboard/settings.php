<?

defined('C5_EXECUTE') or die(_("Access Denied."));
$h = Loader::helper('concrete/interface'); ?>

<? if ($this->controller->getTask() == 'set_developer') { ?>

<div id="ccm-module-row1">
<div class="ccm-module">

<form method="post" id="debug-form" action="<?=$this->url('/dashboard/settings', 'update_debug')?>">
	<?=$this->controller->token->output('update_debug')?>
	
	<h1><span><?=t('Debug Level')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	<p><?=t('Note: these are global settings. If enabled, PHP errors will be displayed to all visitors of the site.')?></p>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="debug_level" value="<?=DEBUG_DISPLAY_PRODUCTION?>" <? if ($debug_level == DEBUG_DISPLAY_PRODUCTION) { ?> checked <? } ?> /> <?=t('Production')?> </div>
	<div class="ccm-dashboard-description"><?=t('PHP errors and database exceptions will be suppressed.')?></div>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="debug_level" value="<?=DEBUG_DISPLAY_ERRORS?>" <? if ($debug_level == DEBUG_DISPLAY_ERRORS) { ?> checked <? } ?> /> <?=t('Development')?> </div>
	<div class="ccm-dashboard-description"><?=t('PHP errors and database exceptions will be displayed.')?></div>
	
	<?
	$b1 = $h->submit(t('Set Debug Level'), 'debug-form');
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>

</div>
<div class="ccm-module">
<form method="post" id="logging-form" action="<?=$this->url('/dashboard/settings', 'update_logging')?>">
	<?=$this->controller->token->output('update_logging')?>
	
	<h1><span><?=t('Logging')?></span></h1>
	<div class="ccm-dashboard-inner">
	<div class="ccm-dashboard-radio"><?=$form->checkbox('ENABLE_LOG_ERRORS', 1, $enable_log_errors)?> <?=t('Log Application Exceptions')?></div>
	<div class="ccm-dashboard-description"><?=t('Saves application exceptions to logs.')?></div>
	<div class="ccm-dashboard-radio"><?=$form->checkbox('ENABLE_LOG_DATABASE_QUERIES', 1, $enable_log_database_queries)?> <?=t('Log Database Activity')?></div>
	<div class="ccm-dashboard-description"><?=t('Logs SQL queries for application profiling.')?></div>
	<div class="ccm-dashboard-radio"><?=$form->checkbox('ENABLE_LOG_EMAILS', 1, $enable_log_emails)?> <?=t('Log Emails Sent')?></div>
	<div class="ccm-dashboard-description">
		<?=t('Enables saving records of emails being sent out. This will save records even if actual email delivery is disabled on your site.')?>
	</div>
	
	<?
	$b1 = $h->submit(t('Save Logging Settings'), 'logging-form');
	print $h->buttons($b1);
	?>
	
	
	</div>
</form>
</div>
</div>

<? } else if ($this->controller->getTask() == 'set_permissions') { ?>

<h1><span><?=t('Site Permissions')?></span></h1>
<div class="ccm-dashboard-inner">


<? if (PERMISSIONS_MODEL != 'simple') { ?>

<p>
<?=t('Your Concrete site does not use the simple permissions model. You must change your permissions for each specific page and content area.')?>
</p>


<? } else { ?>

<form method="post" id="permissions-form" action="<?=$this->url('/dashboard/settings', 'update_permissions')?>">
	<?=$this->controller->token->output('update_permissions')?>
	
	<h2><?=t('Viewing Permissions')?></h2>
	
	
	<div class="ccm-dashboard-radio"><input type="radio" name="view" value="ANYONE" style="vertical-align: middle" <? if ($guestCanRead) { ?> checked <? } ?> /> <?=t('Public')?></div>
	<div class="ccm-dashboard-description"><?=t('Anyone may view the website.')?></div>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="view" value="USERS" style="vertical-align: middle" <? if ($registeredCanRead) { ?> checked <? } ?> /> <?=t('Members Only')?></div>
	<div class="ccm-dashboard-description"><?=t('Only registered users may view the website.')?></div>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="view" value="PRIVATE" style="vertical-align: middle" <? if ((!$guestCanRead) && (!$registeredCanRead)) { ?> checked <? } ?> /> <?=t('Private')?></div>
	<div class="ccm-dashboard-description"><?=t('Only the administrative group may view the website.')?></div>
	
	
	<br/><br/>
	
	
	<h2><?=t('Edit Access')?></h2>
	<p>
	<?=t('Choose which users and groups below may edit your site. Note: These settings can be overridden on specific pages.')?>
	</p>
	
	<?
	
	foreach ($gArray as $g) {
	?>
	
	<input type="checkbox" name="gID[]" value="<?=$g->getGroupID()?>" <? if ($g->canWrite()) { ?> checked <? } ?> /> <?=$g->getGroupName()?><br/>
	
	<? } ?>
	
	<?
	$b1 = $h->submit(t('Update Site Permissions'), 'permissions-form');
	print $h->buttons($b1);
	?>
	<br class="clear" />
</form>

<? } ?>
</div>



<form method="post" id="maintenance-form" action="<?=$this->url('/dashboard/settings', 'update_maintenance')?>">
	<?=$this->controller->token->output('update_maintenance')?>
	
	<h1><span><?=t('Maintenance Mode')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<p>
	<?=t('Maintenance mode makes the front-end of the website inaccessible, while leaving the dashboard available to admin users.')?>
	</p>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="site_maintenance_mode" value="0"  <? if ($site_maintenance_mode == 0) { ?> checked <? } ?> /> <?=t('Disabled')?></div>
	<div class="ccm-dashboard-description"><?=t('When disabled, the site is available to the public.')?></div>
	
	<div class="ccm-dashboard-radio"><input type="radio" id="site-maintenance-mode-enabled" name="site_maintenance_mode" value="1" <? if ($site_maintenance_mode == 1) { ?> checked <? } ?> /> <?=t('Enabled')?> </div>
	<div class="ccm-dashboard-description"><?=t('If enabled, only your dashboard will be accessible.')?></div>
	
	<?
	$b1 = $h->button_js(t('Save'), 'saveMaintenanceMode');
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>

<script type="text/javascript">
saveMaintenanceMode = function() {
	if ($('#site-maintenance-mode-enabled').get(0).checked) {
		if (confirm('<?=t('Are you sure you want to put your site into maintenance mode? This will make it inaccessible to public visitors.')?>')) {
			$("#maintenance-form").get(0).submit();
		}
	} else {
		$("#maintenance-form").get(0).submit();
	}
}
</script>


<? } else { ?>


<div id="ccm-module-wrapper">
<div style="width: 778px">


<div class="ccm-module" style="width: 320px; margin-bottom: 0px">

<form method="post" id="site-form" action="<?=$this->url('/dashboard/settings', 'update_sitename')?>">
	<?=$this->controller->token->output('update_sitename')?>
	
	<h1><span><?=t('Site Name')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div><?=$form->label('SITE', t('Name Your Website'))?></div>
	<?=$form->text('SITE', $site, array('style'=>'width:285px'))?>
	
	<?
	$b1 = $h->submit(t('Save Site Name'), 'site-form');
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>

<form method="post" id="url-form" action="<?=$this->url('/dashboard/settings', 'update_rewriting')?>">
	<?=$this->controller->token->output('update_rewriting')?>
	
	<h1><span><?=t('Linking')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div class="ccm-dashboard-radio"><?=$form->checkbox('URL_REWRITING', 1, $url_rewriting)?> <?=t('Enable Pretty URLs')?></div>
	<div class="ccm-dashboard-description"><?=t("Automatically translates your path-based Concrete5 URLs so that they don't include 'index.php'.")?></div>
	
	<? if (URL_REWRITING) { ?>
	<h2><?=t('Required Code')?></h2>
	<p><?=t("You must copy the lines of code below and place them in your server's configuration file or .htaccess file.")?></p>
	
	<textarea style="width: 295px; height: 140px;" onclick="this.select()">
	<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase <?=DIR_REL?>/
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	
	RewriteRule ^(.*)$ index.php/$1 [L]
	</IfModule>
	</textarea>
	<br/>
	<br/>
	<? } ?>
	
	<?
	$b1 = $h->submit(t('Save'), 'url-form');
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>


<form method="post" id="marketplace-support-form" action="<?=$this->url('/dashboard/settings', 'update_marketplace_support')?>" enctype="multipart/form-data" >
	<?=$this->controller->token->output('update_marketplace_support')?>

	<h1><span><?=t('Enable Marketplace Support')?> </span></h1>
	
	<div class="ccm-dashboard-inner">	
		 
		<? if( MARKETPLACE_CONFIG_OVERRIDE ){ ?>
		
			<div class="ccm-dashboard-description"><?=t("The marketplace has been manually set in the site's configuration files.")?></div>
		
		<? }else{ ?>
				
			<div class="ccm-dashboard-radio"><?=$form->checkbox('MARKETPLACE_ENABLED', 1, $marketplace_enabled_in_config)?> <?=t('Marketplace Enabled')?></div>
			<div class="ccm-dashboard-description"><? /*t("") */ ?></div>
			
			<?
			$b1 = $h->submit( t('Save'), 'marketplace-support-form');
			print $h->buttons($b1);
			?> 
		
		<? } ?>
		<br class="clear" />
	</div>
</form>


<form method="post" id="favicon-form" action="<?=$this->url('/dashboard/settings', 'update_favicon')?>" enctype="multipart/form-data" >
	<?=$this->controller->token->output('update_favicon')?>

	<h1><span><?=t('Upload Bookmark Icon')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
		
		<input id="remove-existing-favicon" name="remove_favicon" type="hidden" value="0" />
		<?
		$favIconFID=intval(Config::get('FAVICON_FID'));
		if($favIconFID){
			Loader::block('library_file');
			$fileBlock=LibraryFileBlockController::getFile( $favIconFID ); ?>
			<div style="float:right">
			<img src="<?=$fileBlock->getFileRelativePath() ?>" />
			<a onclick="removeFavIcon()"><?=t('Remove')?></a>
			</div>
			<script>
			function removeFavIcon(){
				document.getElementById('remove-existing-favicon').value=1;
				$('#favicon-form').get(0).submit();
			}
			</script>
		<? } ?>
		<input id="favicon_upload" type="file" name="favicon_file"/>
		
		<div class="ccm-dashboard-description"><?=t('Your image should be 16x16 pixels, and should be an gif or a png with a .ico file extension.')?></div>
		
		<?
		$b1 = $h->submit( t('Save'), 'favicon-form');
		print $h->buttons($b1);
		?> 
		<br class="clear" />
	</div>

</form>


</div>


<div class="ccm-module" style="width: 380px; margin-bottom: 0px">

<form method="post" id="user-settings-form" action="<?=$this->url('/dashboard/settings', 'update_user_settings')?>">
	<?=$this->controller->token->output('update_user_settings')?>
	
	<h1><span><?=t('Editing Preferences')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<p><?=t('These editing preferences apply just to your user account.')?></p>
	
	<div class="ccm-dashboard-radio">
	<input type="checkbox" name="ui_breadcrumb" value="1"  <? if ($ui_breadcrumb == 1) { ?> checked <? } ?> /> 
	<?=t('Display breadcrumb navigation bar.')?>
	</div>
	<div class="ccm-dashboard-description">
	<?=t('When enabled, rolling your mouse over the editing bar will show the path to the current page.')?>
	</div>
	
	<?
	$b1 = $h->submit(t('Save'), "user-settings-form");
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>


<form method="post" id="tracking-code-form" action="<?=$this->url('/dashboard/settings', 'update_tracking_code')?>">
	<?=$this->controller->token->output('update_tracking_code')?>

	<h1><span><?=t('Tracking Code')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
		<textarea name="tracking_code" cols="50" rows="4" style="width:98%;height:100px;" ><?=$site_tracking_code ?></textarea>
		
		<div class="ccm-dashboard-description"><?=t('Any HTML you paste here will be inserted at the bottom of every page in your website automatically.')?></div>
		
		<?
		$b1 = $h->submit( t('Save'), 'tracking-code-form');
		print $h->buttons($b1);
		?> 
		<br class="clear" />
	</div>

</form>




</div>

</div>
</div>



<? } ?>

<style type="Text/css">
div.ccm-dashboard-inner {margin-bottom: 10px !important}
</style>