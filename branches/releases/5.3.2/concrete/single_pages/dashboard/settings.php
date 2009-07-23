<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$h = Loader::helper('concrete/interface'); ?>

<style>
.ccm-module form{ width:auto; height:auto; padding:0px; padding-bottom:10px; display:block; }
.ccm-module form div.ccm-dashboard-inner{ margin-bottom:0px !important; }
</style>


<?php  if ($this->controller->getTask() == 'export_database_schema') { ?>

<h1><span><?php echo t('Database Schema')?></span></h1>
<div class="ccm-dashboard-inner">
<a href="<?php echo $this->url('/dashboard/settings', 'set_developer')?>">&laquo; <?php echo t('Return to Developer Settings')?></a>
<code><pre><?php echo htmlentities($schema, ENT_COMPAT, APP_CHARSET)?></pre></code>

</div>

<?php  } else if ($this->controller->getTask() == 'set_developer' || $this->controller->getTask() == 'refresh_database_schema') { ?>

<div id="ccm-module-wrapper">
<div style="width: 778px">

<div id="ccm-module-row1">
<div class="ccm-module">

<form method="post" id="debug-form" action="<?php echo $this->url('/dashboard/settings', 'update_debug')?>">
	<?php echo $this->controller->token->output('update_debug')?>
	
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


<h1><span><?php echo t('Caching')?></span></h1>
	<div class="ccm-dashboard-inner">

	<form method="post" id="update-cache-form" action="<?php echo $this->url('/dashboard/settings', 'update_cache')?>">

	<?php echo $this->controller->token->output('update_cache')?>

	<h2><?php echo t('Cache site for better performance?')?></h2>

	<div class="ccm-dashboard-radio"><input type="radio" name="ENABLE_CACHE" value="0" <?php  if (ENABLE_CACHE == false) { ?> checked <?php  } ?> /> <?php echo t('Disabled')?> </div>
	<div class="ccm-dashboard-description"><?php echo t('Your site content will not be cached. This may be useful while development is proceeding.')?></div>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="ENABLE_CACHE" value="1" <?php  if (ENABLE_CACHE == true) { ?> checked <?php  } ?> /> <?php echo t('Enabled')?> </div>
	<div class="ccm-dashboard-description"><?php echo t('Once your site is live, it is usually best to enable the cache.')?></div>

	<?php 
	$b1 = $h->submit(t('Update Cache'), 'update-cache-form');
	print $h->buttons($b1);
	?>
	
	</form>
	
	<form method="post" id="clear-cache-form" action="<?php echo $this->url('/dashboard/settings', 'clear_cache')?>">

	<?php echo $this->controller->token->output('clear_cache')?>

	<h2><?php echo t('Clear Cache')?></h2>
	<p><?php echo t('If your site is displaying out-dated information, or behaving unexpectedly, it may help to clear your cache.')?></p>
	
	<?php 
	$b1 = $h->submit(t('Clear Cache'), 'clear-cache-form');
	print $h->buttons($b1);
	?>
	
	</form>
	
	<br class="clear" />
	</div>

</form>

</div>
<div class="ccm-module">
<form method="post" id="logging-form" action="<?php echo $this->url('/dashboard/settings', 'update_logging')?>">
	<?php echo $this->controller->token->output('update_logging')?>
	
	<h1><span><?php echo t('Logging')?></span></h1>
	<div class="ccm-dashboard-inner">
	<div class="ccm-dashboard-radio"><?php echo $form->checkbox('ENABLE_LOG_ERRORS', 1, $enable_log_errors)?> <?php echo t('Log Application Exceptions')?></div>
	<div class="ccm-dashboard-description"><?php echo t('Saves application exceptions to logs.')?></div>
	<div class="ccm-dashboard-radio"><?php echo $form->checkbox('ENABLE_LOG_DATABASE_QUERIES', 1, $enable_log_database_queries)?> <?php echo t('Log Database Activity')?></div>
	<div class="ccm-dashboard-description"><?php echo t('Logs SQL queries for application profiling.')?><br />
		<?php echo t('Warning: this may make your database huge!');?></div>
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


<?php  if (ENABLE_DEVELOPER_OPTIONS) { ?>
	
	<h1><span><?php echo t('Database Tables and Content')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="export-db-form" action="<?php echo $this->url('/dashboard/settings', 'export_database_schema')?>">

	<h2><?php echo t('Export Database Schema')?></h2>
	<p><?php echo t('Click below to view your database schema in a format that can imported into concrete5 later.')?></p>
	
	<?php 
	$b1 = $h->submit(t('Export Database Tables'), 'export-db-form');
	print $h->buttons($b1);
	?>
	</form>

	<form method="post" id="refresh-schema-form" action="<?php echo $this->url('/dashboard/settings', 'refresh_database_schema')?>">
		<?php echo $this->controller->token->output('refresh_database_schema')?>
		<h2><?php echo t('Refresh Schema')?></h2>
		<?php  
		$extra = array();
		if (!file_exists('config/' . FILENAME_LOCAL_DB)) {
			$extra = array('disabled' => 'true');
		}
		?>
		<div class="ccm-dashboard-radio"><?php echo $form->checkbox('refresh_global_schema', 1, false)?> <?php echo t('Refresh core database tables and blocks.')?></div>
		<div class="ccm-dashboard-description"><?php echo t('Refreshes %s files contained in %s and all block directories.', FILENAME_BLOCK_DB, 'concrete/config/')?></div>
		<div class="ccm-dashboard-radio"><?php echo $form->checkbox('refresh_local_schema', 1, false, $extra)?> <?php echo t('Reload custom tables.')?></div>
		<div class="ccm-dashboard-description"><?php echo t('Reloads database tables contained in %s.', 'config/' . FILENAME_LOCAL_DB)?></div>

		
		<?php 
		$b1 = $h->submit(t('Refresh Databases'), 'refresh-schema-form');
		print $h->buttons($b1);
		?>
		
	</form>
		
	</div>


<?php  } ?>

</div>
</div>
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
	<?php echo $this->controller->token->output('update_permissions')?>
	
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



<form method="post" id="maintenance-form" action="<?php echo $this->url('/dashboard/settings', 'update_maintenance')?>">
	<?php echo $this->controller->token->output('update_maintenance')?>
	
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
	$b1 = $h->button_js(t('Save'), 'saveMaintenanceMode()');
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>

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

<form method="post" id="ipblacklist-form" action="<?php echo $this->url('/dashboard/settings', 'update_ipblacklist')?>">
	<?php echo $this->controller->token->output('update_ipblacklist')?>
	
	<h1><span><?php echo t('IP Address Blacklist')?></span></h1>

	<div class="ccm-dashboard-inner">	
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-dashboard-inner-columns">
		<tr>
		<td valign="top" class="ccm-dashboard-inner-leftcol">	
			<h2><?php echo t('Smart IP Banning')?></h2>
			<div class="ccm-dashboard-radio">
				<?php echo $form->checkbox('ip_ban_lock_ip_enable', 1, $ip_ban_enable_lock_ip_after)?> <?php echo t('Lock IP after')?>
				
				<?php echo $form->text('ip_ban_lock_ip_attempts', $ip_ban_lock_ip_after_attempts, array('style'=>'width:30px'))?>
				<?php echo t('failed login attempts');?>		
				in		
				<?php echo $form->text('ip_ban_lock_ip_time', $ip_ban_lock_ip_after_time, array('style'=>'width:30px'))?>				
				<?php echo t('seconds');?>				
			</div>	
			<div class="ccm-dashboard-radio">
				<?php echo $form->radio('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type_timed, $ip_ban_lock_ip_how_long_type)?> <?php echo t('Ban IP For')?>	
				<?php echo $form->text('ip_ban_lock_ip_how_long_min', $ip_ban_lock_ip_how_long_min, array('style'=>'width:30px'))?>				
				<?php echo t('minutes');?>
				<?php echo $form->radio('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type_forever, $ip_ban_lock_ip_how_long_type)?> <?php echo t('Forever')?>					
			</div>
			
			<Div style="height: 10px">&nbsp;</div>
			<h3><?php echo t('Automatically Banned IP Addresses')?></h3>
			<table class="grid-list" width="100%" cellspacing="1" cellpadding="0" border="0">	
				<tr>
					<td class="subheader"><?php echo $form->checkbox('ip_ban_select_all',1,false)?> <?php echo t('IP')?></td>
					<td class="subheader"><?php echo t('Reason For Ban')?></td>
					<td class="subheader"><?php echo t('Expires In')?></td>
					<td class="subheader"> 
						<select name="ip_ban_change_to" id="ip_ban_change_to">				
							<option value="<?php echo $ip_ban_change_makeperm?>"><?php echo t('Make Ban Permanent')?></option>
							<option value="<?php echo $ip_ban_change_remove?>"><?php echo t('Remove Ban')?></option>
						</select>
						<input type="button" value="<?php echo t('Go')?>" name="submit-ipblacklist" id="submit-ipblacklist" />
					</td>
				</tr>
				<?php  if (count($user_banned_limited_ips) == 0) {?>
				<tr>
					<td colspan="4">None</td>
				</tr>				
				<?php  } else { ?>
					<?php  foreach ($user_banned_limited_ips as $user_banned_ip) { ?>
						<tr>
							<td><?php echo $form->checkbox('ip_ban_changes[]',$user_banned_ip->getUniqueID(),false)?> <?php echo $user_banned_ip->getIPRangeForDisplay()?></td>
							<td><?php echo $user_banned_ip->getReason()?></td>
							<td><?php echo ($this->controller->formatTimestampAsMinutesSeconds($user_banned_ip->expires))?></td>			
							<td>&nbsp;</td>
						</tr>		
					<?php  } ?>
				<?php  } ?>
			</table>	
		</td>
		<td class="ccm-dashboard-inner-gutter"><div>&nbsp;</div></td>
		<td valign="top" class="ccm-dashboard-inner-rightcol">
			<h2><?php echo t('Permanent IP Ban')?></h2>
			<p class="notes">
			<?php echo t('Enter IP addresses, one per line, in the form below to manually ban an IP address. To indicate a range, use a wildcard character (e.g. 192.168.15.* will block 192.168.15.1, 192.168.15.2, etc...)')?>			
			</p>					
			<textarea id="ip_ban_manual" name="ip_ban_manual" rows="10" cols="40" style="width:100%"><?php echo $user_banned_manual_ips?></textarea>
		</td>
		</tr>
		</table>
		<br/>
		
		<?php 	
		$b1 = $h->button_js(t('Save'), 'saveIpBlacklist()');
		print $h->buttons($b1);
		?>		
		<br class="clear" />		
	</div>

</form>

<script type="text/javascript">

var saveIpBlacklist = function(){
	$("form#ipblacklist-form").get(0).submit();	
}

//jQuery block for non-submit form logic
$(document).ready(function(){
	var sParentSelector;
	sParentSelector = 'form#ipblacklist-form';	
	//delegate for any clicks to this form
	$(sParentSelector).bind('click', function(e){
		//clicks the parent IP checkbox
		if ( $(e.target).is('input#ip_ban_select_all') ) {
			allIPs(e.target);
		}
		else if( $(e.target).is('input#submit-ipblacklist') ) {
			saveIpBlacklist();
		}
	});	
	
	$(sParentSelector).bind('change', function(e){
		if ($(e.target).is('select')) {			
			//$('input[name=submit-ipblacklist]').attr('value',$(':selected',e.target).text());
		}
	});
	
	function allIPs(t){
		if(t.checked){
			$('form#ipblacklist-form table input').attr('checked',true);
		}
		else{
			$('form#ipblacklist-form table input').attr('checked',false);
		}	
	}
});
</script>
<?php  } else { ?>


<div id="ccm-module-wrapper">
<div style="width: 778px">


<div class="ccm-module" style="width: 320px; margin-bottom: 0px">

<form method="post" id="site-form" action="<?php echo $this->url('/dashboard/settings', 'update_sitename')?>">
	<?php echo $this->controller->token->output('update_sitename')?>
	
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


<form method="post" id="tracking-code-form" action="<?php echo $this->url('/dashboard/settings', 'update_tracking_code')?>">
	<?php echo $this->controller->token->output('update_tracking_code')?>

	<h1><span><?php echo t('Tracking Code')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
		<textarea name="tracking_code" cols="50" rows="4" style="width:98%;height:100px;" ><?php echo $site_tracking_code ?></textarea>
		
		<div class="ccm-dashboard-description"><?php echo t('Any HTML you paste here will be inserted at the bottom of every page in your website automatically.')?></div>
		
		<?php 
		$b1 = $h->submit( t('Save'), 'tracking-code-form');
		print $h->buttons($b1);
		?> 
		<br class="clear" />
	</div>

</form>


<form method="post" id="marketplace-support-form" action="<?php echo $this->url('/dashboard/settings', 'update_marketplace_support')?>" enctype="multipart/form-data" >
	<?php echo $this->controller->token->output('update_marketplace_support')?>

	<h1><span><?php echo t('Marketplace Integration')?> </span></h1>
	
	<div class="ccm-dashboard-inner">	
		 
		<?php  if( MARKETPLACE_CONFIG_OVERRIDE ){ ?>
		
			<div class="ccm-dashboard-description"><?php echo t("The marketplace has been manually set in the site's configuration files.")?></div>
		
		<?php  }else{ ?>
				
			<div class="ccm-dashboard-radio"><?php echo $form->checkbox('MARKETPLACE_ENABLED', 1, $marketplace_enabled_in_config)?> <?php echo t('Marketplace Enabled')?></div>
			<div class="ccm-dashboard-description"><?php echo  t("Show me themes and add-ons available for Concrete5.") ?></div>
			
			<?php 
			$b1 = $h->submit( t('Save'), 'marketplace-support-form');
			print $h->buttons($b1);
			?> 
		
		<?php  } ?>
		<br class="clear" />
	</div>
</form>


<form method="post" id="favicon-form" action="<?php echo $this->url('/dashboard/settings', 'update_favicon')?>" enctype="multipart/form-data" >
	<?php echo $this->controller->token->output('update_favicon')?>

	<h1><span><?php echo t('Upload Bookmark Icon')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
		
		<input id="remove-existing-favicon" name="remove_favicon" type="hidden" value="0" />
		<?php 
		$favIconFID=intval(Config::get('FAVICON_FID'));
		if($favIconFID){
			$f = File::getByID($favIconFID);
			?>
			<div>
			<img src="<?php echo $f->getRelativePath() ?>" />
			<a onclick="removeFavIcon()"><?php echo t('Remove')?></a>
			</div>
			<script>
			function removeFavIcon(){
				document.getElementById('remove-existing-favicon').value=1;
				$('#favicon-form').get(0).submit();
			}
			</script>
		<?php  }else{ ?>
			<input id="favicon_upload" type="file" name="favicon_file"/>		
			<div class="ccm-dashboard-description" style="margin-top:4px"><?php echo t('Your image should be 16x16 pixels, and should be an gif or a png with a .ico file extension.')?></div>
		<?php  } ?>
		
		<?php 
		$b1 = $h->submit( t('Save'), 'favicon-form');
		print $h->buttons($b1);
		?> 
		<br class="clear" />
	</div>

</form>


</div>


<div class="ccm-module" style="width: 380px; margin-bottom: 0px">

<form method="post" id="user-settings-form" action="<?php echo $this->url('/dashboard/settings', 'update_user_settings')?>">
	<?php echo $this->controller->token->output('update_user_settings')?>
	
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


<form method="post" id="url-form" action="<?php echo $this->url('/dashboard/settings', 'update_rewriting')?>">
	<?php echo $this->controller->token->output('update_rewriting')?>
	
	<h1><span><?php echo t('Linking')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div class="ccm-dashboard-radio"><?php echo $form->checkbox('URL_REWRITING', 1, $url_rewriting)?> <?php echo t('Enable Pretty URLs')?></div>
	<div class="ccm-dashboard-description"><?php echo t("Automatically translates your path-based Concrete5 URLs so that they don't include 'index.php'.")?></div>
	
	<?php  if (URL_REWRITING) { ?>
	<h2><?php echo t('Required Code')?></h2>
	<p><?php echo t("You must copy the lines of code below and place them in your server's configuration file or .htaccess file.")?></p>
	
	<textarea style="width: 97%; height: 140px;" onclick="this.select()">
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

<?php  /*
<form method="post" id="image-editing-form" action="<?php echo $this->url('/dashboard/settings', 'update_image_editing')?>">
	<?php echo $this->controller->token->output('update_image_editing')?>
	
	<h1><span><?php echo t('Image Editing')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div><?php echo $form->label('API_KEY_PICNIK', t('Picnik API Key'))?></div>
	<?php echo $form->text('API_KEY_PICNIK', $api_key_picnik, array('style'=>'width:285px; float: left'))?>
	
	<?php 
	$b1 = $h->submit(t('Save'), 'image-editing-form');
	print $b1;
	?>
	<br class="clear" />
	</div>

</form>
*/ ?>

<form method="post" id="txt-editor-form" action="<?php echo $this->url('/dashboard/settings', 'txt_editor_config')?>">
	<?php echo $this->controller->token->output('txt_editor_config')?>
	
	<h1><span><?php echo t("Rich Text Editor")?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
		
		
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td valign="top">

		<h2>Toolbar Set</h2>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="SIMPLE" style="vertical-align: middle" <?php echo ( $txtEditorMode=='SIMPLE' || !strlen($txtEditorMode) )?'checked':''?> /> <?php echo t('Simple')?></div>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="ADVANCED" style="vertical-align: middle" <?php echo ($txtEditorMode=='ADVANCED')?'checked':''?> /> <?php echo t('Advanced')?></div>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="OFFICE" style="vertical-align: middle" <?php echo ($txtEditorMode=='OFFICE')?'checked':''?> /> <?php echo t('Office')?></div>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="CUSTOM" style="vertical-align: middle" <?php echo ($txtEditorMode=='CUSTOM')?'checked':'' ?> /> <?php echo t('Custom')?></div>
		
		<div id="cstmEditorTxtAreaWrap" style=" display:<?php echo ($txtEditorMode=='CUSTOM')?'block':'none' ?>" >
			<textarea wrap="off" name="CONTENTS_TXT_EDITOR_CUSTOM_CODE" cols="25" rows="20" style="width: 97%; height: 250px;"><?php echo $txtEditorCstmCode?></textarea>
			<div class="ccm-note"><a target="_blank" href="http://tinymce.moxiecode.com/"><?php echo t('TinyMCE Reference')?></a></div>
		</div>

		</td>
		<td><div style="width: 50px">&nbsp;</div></td>
		<td valign="top">
		
		<h2>Editor Dimensions</h2>
		
		<table cellspacing="0" cellpadding="0">
		<tr>
		<td><?php echo t('Width')?></td><td><input type="text" name="CONTENTS_TXT_EDITOR_WIDTH" size="3" value="<?php echo ($textEditorWidth<580) ? 580 : intval($textEditorWidth) ?>"/></td><td>&nbsp;px</td>
		</tr>
		<tr>
		<td><?php echo t('Height')?></td><td><input type="text" name="CONTENTS_TXT_EDITOR_HEIGHT" size="3" value="<?php echo ($textEditorHeight<100) ? 380 : intval($textEditorHeight) ?>"/></td><td>&nbsp;px</td>
		</tr>
		</table>
		
		<div class="ccm-note"><?php echo t('The minimum width is 580px.')?></div>
		
		</td>
		</tr>
		</table>
		
		<?php 
		$b1 = $h->submit(t('Save'), 'txt-editor-form');
		print $h->buttons($b1);
		?>
		<br class="clear" />
	</div>

	<script>
		$(function(){ 
			$("input[name='CONTENTS_TXT_EDITOR_MODE']").each(function(i,el){ 
				el.onchange=function(){isTxtEditorModeCustom();}
			})	 	
		});	
		function isTxtEditorModeCustom(){
			if($("input[name='CONTENTS_TXT_EDITOR_MODE']:checked").val()=='CUSTOM'){
				$('#cstmEditorTxtAreaWrap').css('display','block');
			}else{
				$('#cstmEditorTxtAreaWrap').css('display','none');
			}
		}
	</script>

</form>


</div>

</div>
</div>



<?php  } ?>

<style type="Text/css">
div.ccm-dashboard-inner {margin-bottom: 10px !important}
</style>