<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="clearfix">

<? 

$enablePermissions = false;
if (!$draft->overridePageTypePermissions()) { ?>

	<div class="alert alert-info">
	<p>
	<?=t("Permissions for this draft are currently inherited from its page type.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-small" onclick="ccm_setPageDraftPermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<? } else { 
	$enablePermissions = true;
	?>

	<div class="alert alert-info">
	<p><?=t("Permissions for this draft currently override its page type permissions.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-small" onclick="ccm_revertToGlobalPageTypePermissions()"><?=t('Revert to Page Type Permissions')?></a>
	</div>
<? } ?>

</div>

<?=Loader::element('permission/help');?>

<? $cat = PermissionKeyCategory::getByHandle('page_draft');?>

<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&pDraftID=<?=$draft->getPageDraftID()?>">

<table class="ccm-permission-grid table table-striped">
<?
$permissions = PermissionKey::getList('page_draft');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($draft);
	?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><? if ($enablePermissions) { ?><a dialog-title="<?=$pk->getPermissionKeyName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><? } ?><?=$pk->getPermissionKeyName()?><? if ($enablePermissions) { ?></a><? } ?></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <? if ($enablePermissions) { ?>class="ccm-permission-grid-cell"<? } ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
<? if ($enablePermissions) { ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<? } ?>

</table>
</form>

<? if ($enablePermissions) { ?>
<div id="ccm-page-draft-permissions-advanced-buttons" class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn pull-left"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary pull-right"><?=t('Save')?></button>
</div>
<? } ?>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/page_draft?duplicate=' + dupe + '&pDraftID=<?=$draft->getPageDraftID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}

$(function() {
	$('#ccm-permission-list-form').ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}		
	});
});

ccm_revertToGlobalPageTypePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_global_page_type_permissions")?>&pDraftID=<?=$draft->getPageDraftID()?>', function() { 
		ccm_refreshPageDraftPermissions();
	});
}

ccm_setPageDraftPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_global_page_type_permissions")?>&pDraftID=<?=$draft->getPageDraftID()?>', function() { 
		ccm_refreshPageDraftPermissions();
	});
}

ccm_refreshPageDraftPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/draft/permissions?pDraftID=<?=$draft->getPageDraftID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}

</script>