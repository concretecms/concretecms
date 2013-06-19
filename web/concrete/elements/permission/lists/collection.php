<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
$editPermissions = false;
if ($c->getCollectionInheritance() == 'OVERRIDE') { 
	$editPermissions = true;
}
?>
<div class="ccm-ui">
<form>
<div class="ccm-pane-options" style="padding-bottom: 0px">
<div class="clearfix">
<label for="ccm-page-permissions-inherit"><?=t('Assign Permissions')?></label>
<div class="input">
   <select id="ccm-page-permissions-inherit" style="width: 220px">
	<? if ($c->getCollectionID() > 1) { ?><option value="PARENT" <? if ($c->getCollectionInheritance() == "PARENT") { ?> selected<? } ?>><?=t('By Area of Site (Hierarchy)')?></option><? } ?>
	<? if ($c->getMasterCollectionID() > 1) { ?><option value="TEMPLATE"  <? if ($c->getCollectionInheritance() == "TEMPLATE") { ?> selected<? } ?>><?=t('From Page Type Defaults')?></option><? } ?>
	<option value="OVERRIDE" <? if ($c->getCollectionInheritance() == "OVERRIDE") { ?> selected<? } ?>><?=t('Manually')?></option>
  </select>
</div>
</div>
<? if (!$c->isMasterCollection()) { ?>
<div class="clearfix">
<label for="ccm-page-permissions-subpages-override-template-permissions"><?=t('Subpage Permissions')?></label>
<div class="input">
	<select id="ccm-page-permissions-subpages-override-template-permissions" style="width: 260px">
		<option value="0"<? if (!$c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit page type default permissions.')?></option>
		<option value="1"<? if ($c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit the permissions of this page.')?></option>
	</select>
</div>
</div>

<? } ?>
</div>
</form>

<?
	  $cpc = $c->getPermissionsCollectionObject();
	if ($c->getCollectionInheritance() == "PARENT") { ?>
	<div><strong><?=t('This page inherits its permissions from:');?> <a target="_blank" href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cpc->getCollectionID()?>"><?=$cpc->getCollectionName()?></a></strong></div><br/><br/>
	<? } ?>		


<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $cat = PermissionKeyCategory::getByHandle('page'); ?>
<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&cID=<?=$c->getCollectionID()?>">

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('page');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($c);
	?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><? if ($editPermissions) { ?><a dialog-title="<?=tc('PermissionKeyName', $pk->getPermissionKeyName())?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><? } ?><?=tc('PermissionKeyName', $pk->getPermissionKeyName())?><? if ($editPermissions) { ?></a><? } ?></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <? if ($editPermissions) { ?>class="ccm-permission-grid-cell"<? } ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
	</tr>
<? } ?>
<? if ($editPermissions) { ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<? } ?>
</table>

</form>

<script type="text/javascript">
ccm_permissionLaunchDialog = function(link) {
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?=$c->getCollectionID()?>&ctask=set_advanced_permissions&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}
</script>



<div id="ccm-page-permissions-confirm-dialog" style="display: none">
<?=t('Changing this setting will affect this page immediately. Are you sure?')?>
<div id="dialog-buttons-start">
	<input type="button" class="btn" value="Cancel" onclick="jQuery.fn.dialog.closeTop()" />
	<input type="button" class="btn error ccm-button-right" value="Ok" onclick="ccm_pagePermissionsConfirmInheritanceChange()" />
</div>
</div>


 <? if ($editPermissions) { ?>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn primary ccm-button-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<? } ?>

</div>


<script type="text/javascript">
var inheritanceVal = '';

ccm_pagePermissionsCancelInheritance = function() {
	$('#ccm-page-permissions-inherit').val(inheritanceVal);
}

ccm_pagePermissionsConfirmInheritanceChange = function() { 
	jQuery.fn.dialog.showLoader();
	$.getJSON('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_permission_inheritance")?>&cID=<?=$c->getCollectionID()?>&mode=' + $('#ccm-page-permissions-inherit').val(), function(r) { 
		if (r.deferred) {
			jQuery.fn.dialog.closeAll();
			jQuery.fn.dialog.hideLoader();
			ccmAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		} else {
			jQuery.fn.dialog.closeTop();
			ccm_refreshPagePermissions();
		}
	});
}


$(function() {
	$('#ccm-permission-list-form').ajaxForm({
		dataType: 'json',
		
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
			if (!r.deferred) {
				ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			} else {
				jQuery.fn.dialog.closeTop();
				ccmAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			}

		}		
	});
	
	inheritanceVal = $('#ccm-page-permissions-inherit').val();
	$('#ccm-page-permissions-inherit').change(function() {
		$('#dialog-buttons-start').addClass('dialog-buttons');
		jQuery.fn.dialog.open({
			element: '#ccm-page-permissions-confirm-dialog',
			title: '<?=t("Confirm Change")?>',
			width: 280,
			height: 100,
			onClose: function() {
				ccm_pagePermissionsCancelInheritance();
			}
		});
	});
	
	$('#ccm-page-permissions-subpages-override-template-permissions').change(function() {
		jQuery.fn.dialog.showLoader();
		$.getJSON('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_subpage_defaults_inheritance")?>&cID=<?=$c->getCollectionID()?>&inherit=' + $(this).val(), function(r) { 
			if (r.deferred) {
				jQuery.fn.dialog.closeTop();
				jQuery.fn.dialog.hideLoader();
				ccmAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			} else {
				ccm_refreshPagePermissions();
			}
		});
	});
	
});

ccm_refreshPagePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=edit_permissions&cID=<?=$c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});	
}

</script>
