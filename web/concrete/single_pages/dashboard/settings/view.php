<?

defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/interface'); ?>

<style type="text/css">
.ccm-module form{ width:auto; height:auto; padding:0px; padding-bottom:10px; display:block; }
.ccm-module form div.ccm-dashboard-inner{ margin-bottom:0px !important; }
</style>

<? if ($this->controller->getTask() == 'access_task_permissions' || $this->controller->getTask() == 'save_task_permissions') { ?>

<div style="width: 760px">

<?
$tp1 = TaskPermission::getByHandle('access_task_permissions');
if ($tp1->can()) { 
	$ih = Loader::helper('concrete/interface');
	$tps = array(
		TaskPermission::getByHandle('access_task_permissions'),
		TaskPermission::getByHandle('access_user_search'),
		TaskPermission::getByHandle('access_group_search'),
		TaskPermission::getByHandle('access_page_defaults'),
		TaskPermission::getByHandle('install_packages'),
		TaskPermission::getByHandle('uninstall_packages'),
		TaskPermission::getByHandle('backup'),
		TaskPermission::getByHandle('sudo'),
		TaskPermission::getByHandle('delete_user')
	);
	$tpl = new TaskPermissionList();
	foreach($tps as $tp) {
		$tpl->add($tp);
	}
	?>
	
	<h1><span><?=t('Task Permissions')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="ccm-task-permissions" action="<?=$this->url('/dashboard/settings', 'save_task_permissions')?>">
			<?=$this->controller->token->output('update_permissions');?>
	
			<? print Loader::helper('concrete/dashboard/task_permissions')->getForm($tpl, t('Set permissions for common concrete5 tasks.')); ?>
			
			<div class="ccm-spacer">&nbsp;</div>
			
			
			<? print $ih->submit(t('Save'), 'ccm-task-permissions'); ?>
	
			<div class="ccm-spacer">&nbsp;</div>
		</form>
	</div>
<? } else { ?>
	<h1><span><?=t('Task Permissions')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?=t('You are not allowed to change these permissions.')?>
	</div>
<? } ?>

	</div>




<? } else if ($this->controller->getTask() == 'manage_attribute_types') { 

$types = AttributeType::getList();
$categories = AttributeKeyCategory::getList();
$txt = Loader::helper('text');
?>

<div style="width: 600px">
<form method="post" id="attribute_type_associations_form" action="<?=$this->action('save_attribute_type_associations')?>">
<h1><span><?=t('Attribute Type Associations')?></span></h1>
<div class="ccm-dashboard-inner">
<Table border="0" cellspacing="1" cellpadding="0" border="0" class="grid-list">
<tr>
	<td class="header"><?=t('Name')?></td>
	<? foreach($categories as $cat) { ?>
		<td class="header" width="22%"><?=$txt->unhandle($cat->getAttributeKeyCategoryHandle())?></td>
	<? } ?>

<?

foreach($types as $at) { ?>

<tr>
<td><strong><?=$at->getAttributeTypeName()?></strong></td>
<? foreach($categories as $cat) { ?>
	<td><?=$form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
<? } ?>
</tr>


<? } ?>

</table>
<br/>
	<?
	$b1 = $h->submit(t('Save'), 'attribute_type_associations_form');
	print $h->buttons($b1);
	?>
</div>

</form>


<h1><span><?=t('Custom Attribute Types')?></span></h1>
<div class="ccm-dashboard-inner">
<? $types = PendingAttributeType::getList(); ?>
<? if (count($types) == 0) { ?>
	<?=t('There are no available attribute types awaiting installation.')?>
<? } else { ?>
	<table border="0" cellspacing="0" cellpadding="0">
	<? foreach($types as $at) { ?>
	<tr>
		<td style="padding: 0px 10px 10px 0px"><img src="<?=$at->getAttributeTypeIconSRC()?>" /></td>
		<td style="padding:  0px 10px 10px 0px"><?=$at->getAttributeTypeName()?></td>
		<td style="padding:  0px 10px 10px 0px"><form id="attribute_type_install_form_<?=$at->getAttributeTypeHandle()?>" method="post" action="<?=$this->action('add_attribute_type')?>"><?
			print $form->hidden("atHandle", $at->getAttributeTypeHandle());
			$b1 = $h->submit(t('Install'), 'attribute_type_install_form_' . $at->getAttributeTypeHandle());
			print $b1;
			?>
			</form></td>
	</tr>
	<? } ?>
	</table>
<? } ?>
</div>


</div>

<? } else if ($this->controller->getTask() == 'export_database_schema') { ?>

<h1><span><?=t('Database Schema')?></span></h1>
<div class="ccm-dashboard-inner">
<a href="<?=$this->url('/dashboard/settings', 'set_developer')?>">&laquo; <?=t('Return to Developer Settings')?></a>
<code><pre><?=htmlentities($schema, ENT_COMPAT, APP_CHARSET)?></pre></code>

</div>

<? } else if ($this->controller->getTask() == 'set_developer' || $this->controller->getTask() == 'refresh_database_schema') { ?>

<div id="ccm-module-wrapper">
<div style="width: 778px">

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

<h1><span><?=t('Environment')?></span></h1>
<div class="ccm-dashboard-inner">
<textarea style="width: 97%; height: 140px;" onclick="this.select()" id="ccm-dashboard-environment-info"><?=t('Unable to load environment info')?></textarea>
</div>


	


</div>
<div class="ccm-module">
<form method="post" id="logging-form" action="<?=$this->url('/dashboard/settings', 'update_logging')?>">
	<?=$this->controller->token->output('update_logging')?>
	
	<h1><span><?=t('Logging')?></span></h1>
	<div class="ccm-dashboard-inner">
	<div class="ccm-dashboard-radio"><?=$form->checkbox('ENABLE_LOG_ERRORS', 1, $enable_log_errors)?> <?=t('Log Application Exceptions')?></div>
	<div class="ccm-dashboard-description"><?=t('Saves application exceptions to logs.')?></div>
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

<script type="text/javascript">
$(document).ready(function() {
	$('#ccm-dashboard-environment-info').load('<?php echo $this->action('get_environment_info')?>');	
});
</script>


<? if (ENABLE_DEVELOPER_OPTIONS) { ?>
	
	<h1><span><?=t('Database Tables and Content')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="export-db-form" action="<?=$this->url('/dashboard/settings', 'export_database_schema')?>">

	<h2><?=t('Export Database Schema')?></h2>
	<p><?=t('Click below to view your database schema in a format that can imported into concrete5 later.')?></p>
	
	<?
	$b1 = $h->submit(t('Export Database Tables'), 'export-db-form');
	print $h->buttons($b1);
	?>
	</form>

	<form method="post" id="refresh-schema-form" action="<?=$this->url('/dashboard/settings', 'refresh_database_schema')?>">
		<?=$this->controller->token->output('refresh_database_schema')?>
		<h2><?=t('Refresh Schema')?></h2>
		<? 
		$extra = array();
		if (!file_exists('config/' . FILENAME_LOCAL_DB)) {
			$extra = array('disabled' => 'true');
		}
		?>
		<div class="ccm-dashboard-radio"><?=$form->checkbox('refresh_global_schema', 1, false)?> <?=t('Refresh core database tables and blocks.')?></div>
		<div class="ccm-dashboard-description"><?=t('Refreshes %s files contained in %s and all block directories.', FILENAME_BLOCK_DB, 'concrete/config/')?></div>
		<div class="ccm-dashboard-radio"><?=$form->checkbox('refresh_local_schema', 1, false, $extra)?> <?=t('Reload custom tables.')?></div>
		<div class="ccm-dashboard-description"><?=t('Reloads database tables contained in %s.', 'config/' . FILENAME_LOCAL_DB)?></div>

		
		<?
		$b1 = $h->submit(t('Refresh Databases'), 'refresh-schema-form');
		print $h->buttons($b1);
		?>
		
	</form>
		
	</div>


<? } ?>

</div>
</div>
</div>
</div>

<? } else if ($this->controller->getTask() == 'set_permissions') { ?>

<h1><span><?=t('Site Content Permissions')?></span></h1>
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



<?
$tp = new TaskPermission();
if ($tp->canAccessTaskPermissions()) { ?>

<h1><span><?=t('Other Permissions')?></span></h1>
<div class="ccm-dashboard-inner">
	
<a href="<?=$this->url('/dashboard/settings/', 'access_task_permissions')?>"><?=t('Click here to modify other permissions.')?></a>

</div>

<? } else { ?>

<h1><span><?=t('Other Permissions')?></span></h1>
<div class="ccm-dashboard-inner">
	
<?=t('These permissions include specifying who may login as other users, search users, access page defaults, and more. You do not have access to see or change these permissions.')?>
</div>

<? } ?>




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
	$b1 = $h->button_js(t('Save'), 'saveMaintenanceMode()');
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

<form method="post" id="ipblacklist-form" action="<?=$this->url('/dashboard/settings', 'update_ipblacklist')?>">
	<?=$this->controller->token->output('update_ipblacklist')?>
	
	<h1><span><?=t('IP Address Blacklist')?></span></h1>

	<div class="ccm-dashboard-inner">	
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-dashboard-inner-columns">
		<tr>
		<td valign="top" class="ccm-dashboard-inner-leftcol">	
			<h2><?=t('Smart IP Banning')?></h2>
			<div class="ccm-dashboard-radio">
				<?=$form->checkbox('ip_ban_lock_ip_enable', 1, $ip_ban_enable_lock_ip_after)?> <?=t('Lock IP after')?>
				
				<?=$form->text('ip_ban_lock_ip_attempts', $ip_ban_lock_ip_after_attempts, array('style'=>'width:30px'))?>
				<?=t('failed login attempts');?>		
				in		
				<?=$form->text('ip_ban_lock_ip_time', $ip_ban_lock_ip_after_time, array('style'=>'width:30px'))?>				
				<?=t('seconds');?>				
			</div>	
			<div class="ccm-dashboard-radio">
				<?=$form->radio('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type_timed, $ip_ban_lock_ip_how_long_type)?> <?=t('Ban IP For')?>	
				<?=$form->text('ip_ban_lock_ip_how_long_min', $ip_ban_lock_ip_how_long_min, array('style'=>'width:30px'))?>				
				<?=t('minutes');?>
				<?=$form->radio('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type_forever, $ip_ban_lock_ip_how_long_type)?> <?=t('Forever')?>					
			</div>
			
			<Div style="height: 10px">&nbsp;</div>
			<h3><?=t('Automatically Banned IP Addresses')?></h3>
			<table class="grid-list" width="100%" cellspacing="1" cellpadding="0" border="0">	
				<tr>
					<td class="subheader"><?=$form->checkbox('ip_ban_select_all',1,false)?> <?=t('IP')?></td>
					<td class="subheader"><?=t('Reason For Ban')?></td>
					<td class="subheader"><?=t('Expires In')?></td>
					<td class="subheader"> 
						<select name="ip_ban_change_to" id="ip_ban_change_to">				
							<option value="<?=$ip_ban_change_makeperm?>"><?=t('Make Ban Permanent')?></option>
							<option value="<?=$ip_ban_change_remove?>"><?=t('Remove Ban')?></option>
						</select>
						<input type="button" value="<?=t('Go')?>" name="submit-ipblacklist" id="submit-ipblacklist" />
					</td>
				</tr>
				<?php if (count($user_banned_limited_ips) == 0) {?>
				<tr>
					<td colspan="4">None</td>
				</tr>				
				<?php } else { ?>
					<?php foreach ($user_banned_limited_ips as $user_banned_ip) { ?>
						<tr>
							<td><?=$form->checkbox('ip_ban_changes[]',$user_banned_ip->getUniqueID(),false)?> <?=$user_banned_ip->getIPRangeForDisplay()?></td>
							<td><?=$user_banned_ip->getReason()?></td>
							<td><?=($this->controller->formatTimestampAsMinutesSeconds($user_banned_ip->expires))?></td>			
							<td>&nbsp;</td>
						</tr>		
					<? } ?>
				<? } ?>
			</table>	
		</td>
		<td class="ccm-dashboard-inner-gutter"><div>&nbsp;</div></td>
		<td valign="top" class="ccm-dashboard-inner-rightcol">
			<h2><?=t('Permanent IP Ban')?></h2>
			<p class="notes">
			<?=t('Enter IP addresses, one per line, in the form below to manually ban an IP address. To indicate a range, use a wildcard character (e.g. 192.168.15.* will block 192.168.15.1, 192.168.15.2, etc...)')?>			
			</p>					
			<textarea id="ip_ban_manual" name="ip_ban_manual" rows="10" cols="40" style="width:100%"><?=$user_banned_manual_ips?></textarea>
		</td>
		</tr>
		</table>
		<br/>
		
		<?	
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


<form method="post" id="marketplace-support-form" action="<?=$this->url('/dashboard/settings', 'update_marketplace_support')?>" enctype="multipart/form-data" >
	<?=$this->controller->token->output('update_marketplace_support')?>

	<h1><span><?=t('Connect to Community')?> </span></h1>
	
	<div class="ccm-dashboard-inner">	
		<? 
		Loader::library('marketplace');
		$mi = Marketplace::getInstance();
		if ($mi->isConnected()) { ?>
			
			<?=t('Your site is currently connected to the concrete5 community. <a href="%s">Visit project page</a>.', $marketplacePageURL)?>
		
		<?
		
		} else { 
			Loader::element('dashboard/marketplace_connect_failed');
		}
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


<form method="post" id="favicon-form" action="<?=$this->url('/dashboard/settings', 'update_favicon')?>" enctype="multipart/form-data" >
	<?=$this->controller->token->output('update_favicon')?>

	<h1><span><?=t('Upload Bookmark Icon')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
		
		<input id="remove-existing-favicon" name="remove_favicon" type="hidden" value="0" />
		<?
		$favIconFID=intval(Config::get('FAVICON_FID'));
		if($favIconFID){
			$f = File::getByID($favIconFID);
			?>
			<div>
			<img src="<?=$f->getRelativePath() ?>" />
			<a onclick="removeFavIcon()"><?=t('Remove')?></a>
			</div>
			<script>
			function removeFavIcon(){
				document.getElementById('remove-existing-favicon').value=1;
				$('#favicon-form').get(0).submit();
			}
			</script>
		<? }else{ ?>
			<input id="favicon_upload" type="file" name="favicon_file"/>		
			<div class="ccm-dashboard-description" style="margin-top:4px"><?=t('Your image should be 16x16 pixels, and should be an gif or a png with a .ico file extension.')?></div>
		<? } ?>
		
		<?
		$b1 = $h->submit( t('Save'), 'favicon-form');
		print $h->buttons($b1);
		?> 
		<br class="clear" />
	</div>

</form>

<form method="post" id="user-settings-form" action="<?=$this->url('/dashboard/settings', 'update_user_settings')?>">
	<?=$this->controller->token->output('update_user_settings')?>
	
	<h1><span><?=t('Editing Preferences')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<p><?=t('These editing preferences apply just to your user account.')?></p>
	
	<div class="ccm-dashboard-radio">
	<input type="checkbox" name="ui_breadcrumb" value="1"  <? if ($ui_breadcrumb == 1) { ?> checked <? } ?> /> 
	<?=t('Display breadcrumb navigation bar on sub-pages when rolling over header menu.')?>
	</div>
	
	<?
	$sh = Loader::helper('concrete/dashboard/sitemap');
	$fp = FilePermissions::getGlobal();
	?>
	

	<div class="ccm-dashboard-radio">
	<input type="checkbox" name="ui_sitemap" value="1" <? if ($sh->canRead()) { ?> <? if ($ui_sitemap == 1) { ?> checked <? } ?> <? } else { ?> disabled="disabled" <? } ?> /> 
	<?=t('Include button for dashboard sitemap in header.')?>
	</div>

	<div class="ccm-dashboard-radio">
	<input type="checkbox" name="ui_filemanager" value="1" <? if ($fp->canSearchFiles()) { ?> <? if ($ui_filemanager == 1) { ?> checked <? } ?> <? } else { ?> disabled="disabled" <? } ?> /> 
	<?=t('Include button for file manager in header.')?>
	</div>
	
	<?
	$b1 = $h->submit(t('Save'), "user-settings-form");
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>



</div>


<div class="ccm-module" style="width: 380px; margin-bottom: 0px">


<form method="post" id="url-form" action="<?=$this->url('/dashboard/settings', 'update_rewriting')?>">
	<?=$this->controller->token->output('update_rewriting')?>
	
	<h1><span><?=t('Linking')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div class="ccm-dashboard-radio"><?=$form->checkbox('URL_REWRITING', 1, $url_rewriting)?> <?=t('Enable Pretty URLs')?></div>
	<div class="ccm-dashboard-description"><?=t("Automatically translates your path-based Concrete5 URLs so that they don't include %s.", DISPATCHER_FILENAME)?></div>
	
	<? if (URL_REWRITING) { ?>
	<h2><?=t('Required Code')?></h2>
	<p><?=t("You must copy the lines of code below and place them in your server's configuration file or .htaccess file.")?></p>
	
	<textarea style="width: 97%; height: 140px;" onclick="this.select()"><?=$rewriteRules?></textarea>
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

<form method="post" id="url-form" action="<?=$this->url('/dashboard/settings', 'update_statistics')?>">
	<?=$this->controller->token->output('update_statistics')?>
	
	<h1><span><?=t('Track Statistics')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div class="ccm-dashboard-radio"><?=$form->checkbox('STATISTICS_TRACK_PAGE_VIEWS', 1, STATISTICS_TRACK_PAGE_VIEWS)?> <?=t('Track page view statistics.')?></div>
	<div class="ccm-dashboard-description"><?=t("Tracks page views in concrete5. Disabling this may increase site performance, but you will have to get statistics information from elsewhere.")?></div>
	

	<?
	$b1 = $h->submit(t('Save'));
	print $h->buttons($b1);
	?>
	<br class="clear" />
	</div>

</form>




<h1><span><?=t('Speed Settings')?></span></h1>
	<div class="ccm-dashboard-inner">

	<form method="post" id="update-cache-form" action="<?=$this->url('/dashboard/settings', 'update_cache')?>">

	<?=$this->controller->token->output('update_cache')?>
		
	<p><?=t('Caching stores frequently accessed data so it can be more quickly retrieved. Full page caching can dramatically lighten the load on your server and speed up your website, and is highly recommended in high traffic situations.')?></p>

	<h2><?=t('Basic Cache')?></h2>

	<div class="ccm-dashboard-radio"><input type="radio" name="ENABLE_CACHE" value="0" <? if (ENABLE_CACHE == false) { ?> checked <? } ?> /> <?=t('Disabled')?>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="ENABLE_CACHE" value="1" <? if (ENABLE_CACHE == true) { ?> checked <? } ?> /> <?=t('Enabled')?>
	</div>
	<div class="ccm-cache-settings-full-page">

	<h2><?=t('Full Page Caching')?></h2>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="0" <? if (FULL_PAGE_CACHE_GLOBAL == 0 || FULL_PAGE_CACHE_GLOBAL == '0') { ?> checked <? } ?> /> <?=t('Disabled, unless specified at page level.')?></div>
	<div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="blocks" <? if (FULL_PAGE_CACHE_GLOBAL == 'blocks') { ?> checked <? } ?> /> <?=t('Enabled if blocks allow it, unless specified at page level.')?></div>
	<div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="all" <? if (FULL_PAGE_CACHE_GLOBAL == 'all') { ?> checked <? } ?> /> <?=t('Enabled in all cases (Emergency Cache Mode)')?></div>
	
	
	<h3><?=t('Full Page Cache Lifetime')?></h3>
	
	<div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="default" <? if (FULL_PAGE_CACHE_LIFETIME == 'default') { ?> checked <? } ?> /> <?=t('Default - %s minutes', CACHE_LIFETIME / 60)?></div>
	<div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="custom" <? if (FULL_PAGE_CACHE_LIFETIME == 'custom') { ?> checked <? } ?> /> <?=t('Custom - ')?>
		<?=$form->text('FULL_PAGE_CACHE_LIFETIME_CUSTOM', Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM'), array('style' => 'width: 40px'))?> <?=t('minutes')?>		
	</div>
	<div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="forever" <? if (FULL_PAGE_CACHE_LIFETIME == 'forever') { ?> checked <? } ?> /> <?=t('Until manually cleared')?></div>
	
	</div>

	<?
	$b1 = $h->submit(t('Update Cache'), 'update-cache-form');
	print $h->buttons($b1);
	?>
	
	</form>
	
	<form method="post" id="clear-cache-form" action="<?=$this->url('/dashboard/settings', 'clear_cache')?>">

	<?=$this->controller->token->output('clear_cache')?>

	<h2><?=t('Clear Cache')?></h2>
	<p><?=t('If your site is displaying out-dated information, or behaving unexpectedly, it may help to clear your cache.')?></p>
	
	<?
	$b1 = $h->submit(t('Clear Cache'), 'clear-cache-form');
	print $h->buttons($b1);
	?>
	
	</form>
	
	<br class="clear" />
	</form>
	</div>


<? /*
<form method="post" id="image-editing-form" action="<?=$this->url('/dashboard/settings', 'update_image_editing')?>">
	<?=$this->controller->token->output('update_image_editing')?>
	
	<h1><span><?=t('Image Editing')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div><?=$form->label('API_KEY_PICNIK', t('Picnik API Key'))?></div>
	<?=$form->text('API_KEY_PICNIK', $api_key_picnik, array('style'=>'width:285px; float: left'))?>
	
	<?
	$b1 = $h->submit(t('Save'), 'image-editing-form');
	print $b1;
	?>
	<br class="clear" />
	</div>

</form>
*/ ?>

<form method="post" id="txt-editor-form" action="<?=$this->url('/dashboard/settings', 'txt_editor_config')?>">
	<?=$this->controller->token->output('txt_editor_config')?>
	
	<h1><span><?=t("Rich Text Editor")?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
		
		
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td valign="top" width="50%">

		<h2>Toolbar Set</h2>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="SIMPLE" style="vertical-align: middle" <?=( $txtEditorMode=='SIMPLE' || !strlen($txtEditorMode) )?'checked':''?> /> <?=t('Simple')?></div>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="ADVANCED" style="vertical-align: middle" <?=($txtEditorMode=='ADVANCED')?'checked':''?> /> <?=t('Advanced')?></div>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="OFFICE" style="vertical-align: middle" <?=($txtEditorMode=='OFFICE')?'checked':''?> /> <?=t('Office')?></div>
		
		<div class="ccm-dashboard-radio"><input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="CUSTOM" style="vertical-align: middle" <?=($txtEditorMode=='CUSTOM')?'checked':'' ?> /> <?=t('Custom')?></div>
		
		</td>
		<td><div style="width: 50px">&nbsp;</div></td>
		<td valign="top" width="50%">
		
		<h2>Editor Dimensions</h2>
		
		<table cellspacing="0" cellpadding="0">
		<tr>
		<td><?=t('Width')?></td><td><input type="text" name="CONTENTS_TXT_EDITOR_WIDTH" size="3" value="<?=($textEditorWidth<580) ? 580 : intval($textEditorWidth) ?>"/></td><td>&nbsp;px</td>
		</tr>
		<tr>
		<td><?=t('Height')?></td><td><input type="text" name="CONTENTS_TXT_EDITOR_HEIGHT" size="3" value="<?=($textEditorHeight<100) ? 380 : intval($textEditorHeight) ?>"/></td><td>&nbsp;px</td>
		</tr>
		</table>
		
		<div class="ccm-note"><?=t('The minimum width is 580px.')?></div>
		
		</td>
		</tr>
		<tr>
			<td colspan="3">
			
			<div id="cstmEditorTxtAreaWrap" style=" display:<?=($txtEditorMode=='CUSTOM')?'block':'none' ?>" >
				<textarea wrap="off" name="CONTENTS_TXT_EDITOR_CUSTOM_CODE" cols="25" rows="20" style="width: 97%; height: 250px;"><?=$txtEditorCstmCode?></textarea>
				<div class="ccm-note"><a target="_blank" href="http://tinymce.moxiecode.com/"><?=t('TinyMCE Reference')?></a></div>
			</div>
			</td>
		</tr>
		
		</table>
		
		<?
		$b1 = $h->submit(t('Save'), 'txt-editor-form');
		print $h->buttons($b1);
		?>
		<br class="clear" />
	</div>

	<script>
		ccm_settingsSetupCacheForm = function() {
			var obj = $('input[name=ENABLE_CACHE]:checked');
			if (obj.val() == 0) {
				$('div.ccm-cache-settings-full-page input').attr('disabled', true);
				$('input[name=FULL_PAGE_CACHE_LIFETIME][value=default]').attr('checked', true);
			} else {
				$('div.ccm-cache-settings-full-page input').attr('disabled', false);
			}
			var obj2 = $('input[name=FULL_PAGE_CACHE_LIFETIME]:checked');
			if (obj2.val() == 'custom') {
				$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').attr('disabled', false);
			} else {
				$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').attr('disabled', true);
				$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').val('');
			}			
		}
		
		$(function(){ 
			$("input[name='CONTENTS_TXT_EDITOR_MODE']").each(function(i,el){ 
				el.onchange=function(){isTxtEditorModeCustom();}
			})	 	
			$("input[name=ENABLE_CACHE]").click(function() {
				ccm_settingsSetupCacheForm();
			});
			$("input[name=FULL_PAGE_CACHE_LIFETIME]").click(function() {
				ccm_settingsSetupCacheForm();
			});
			$("input[name=FULL_PAGE_CACHE_LIFETIME][value=custom]").click(function() {
				$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').get(0).focus();
			});
			ccm_settingsSetupCacheForm();
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



<? } ?>

<style type="Text/css">
div.ccm-dashboard-inner {margin-bottom: 10px !important}
</style>