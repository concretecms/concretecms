<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
	<header><?=t('Page Permissions')?></header>

	<?
	  $cpc = $c->getPermissionsCollectionObject();
	if ($c->getCollectionInheritance() == "PARENT") { ?>
		<div class="alert alert-info"><?=t('This page inherits its permissions from:');?> <a target="_blank" href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cpc->getCollectionID()?>"><?=$cpc->getCollectionName()?></a></div>
	<? } ?>		


	<div>
		<div class="form-group">
			<label for="ccm-page-permissions-inherit"><?=t('Assign Permissions')?></label>
			<select id="ccm-page-permissions-inherit" class="form-control">
			<? if ($c->getCollectionID() > 1) { ?><option value="PARENT" <? if ($c->getCollectionInheritance() == "PARENT") { ?> selected<? } ?>><?=t('By Area of Site (Hierarchy)')?></option><? } ?>
			<? if ($c->getMasterCollectionID() > 1) { ?><option value="TEMPLATE"  <? if ($c->getCollectionInheritance() == "TEMPLATE") { ?> selected<? } ?>><?=t('From Page Type Defaults')?></option><? } ?>
			<option value="OVERRIDE" <? if ($c->getCollectionInheritance() == "OVERRIDE") { ?> selected<? } ?>><?=t('Manually')?></option>
			</select>
		</div>
	<? if (!$c->isMasterCollection()) { ?>
		<div class="form-group">
			<label for="ccm-page-permissions-subpages-override-template-permissions"><?=t('Subpage Permissions')?></label>
			<select id="ccm-page-permissions-subpages-override-template-permissions" class="form-control">
				<option value="0"<? if (!$c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit page type default permissions.')?></option>
				<option value="1"<? if ($c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit the permissions of this page.')?></option>
			</select>
		</div>
	<? } ?>
	</div>

	<hr/>
	
	<p class="lead"><?=t('Current Permission Set')?></p>

	<? $cat = PermissionKeyCategory::getByHandle('page'); ?>
	<form method="post" id="ccm-permission-list-form" data-dialog-form="permissions" data-panel-detail-form="permissions" action="<?=$cat->getToolsURL("save_permission_assignments")?>&cID=<?=$c->getCollectionID()?>">

	<table class="ccm-permission-grid table table-striped">
	<?
	$permissions = PermissionKey::getList('page');
	foreach($permissions as $pk) { 
		$pk->setPermissionObject($c);
		?>
		<tr>
		<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><? if ($editPermissions) { ?><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><? } ?><?=$pk->getPermissionKeyDisplayName()?><? if ($editPermissions) { ?></a><? } ?></strong></td>
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
	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>
	</form>
</section>


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
			ConcreteAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
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
				ConcreteAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			} else {
				jQuery.fn.dialog.closeTop();
				ConcreteAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
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
				ConcretePanelManager.exitPanelMode();
				jQuery.fn.dialog.hideLoader();
				ConcreteAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			} else {
				ccm_refreshPagePermissions();
			}
		});
	});
	
});

ccm_refreshPagePermissions = function() {
	var panel = ConcretePanelManager.getByIdentifier('page');
	panel.openPanelDetail({
		'identifier': 'page-permissions',
		'url': '<?=URL::to("/system/panels/details/page/permissions")?>'
	});
}

ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?=$c->getCollectionID()?>&ctask=set_advanced_permissions&duplicate=' + dupe + '&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}


</script>