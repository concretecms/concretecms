<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
	<? 
$enablePermissions = false;
if ($a->getAreaCollectionInheritID() != $c->getCollectionID() && $a->getAreaCollectionInheritID() > 0) {
		$pc = $c->getPermissionsCollectionObject(); 
		$areac = Page::getByID($a->getAreaCollectionInheritID());
		?>
		

		<div class="block-message alert-message notice">
		<p>
		<? if ($areac->isMasterCollection()) { ?>
			<? $ctName = $areac->getCollectionTypeName(); ?>
			<?=t("The following area permissions are inherited from an area set in <strong>%s</strong> defaults.", $ctName)?>
		<? } else { ?>
			<?=t("The following area permissions are inherited from an area set on ")?>
			<a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$areac->getCollectionID()?>"><?=$areac->getCollectionName()?></a>. 
		<? } ?>
		</p>
		<br/>
		<a href="javascript:void(0)" class="btn small" onclick="ccm_setAreaPermissionsToOverride()"><?=t('Override Permissions')?></a>
		</div>
		
<? 	} else if (!$a->overrideCollectionPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?=t("The following area permissions are inherited from the page's permissions.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_setAreaPermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<? } else { 
	$enablePermissions = true;
	?>

	<div class="block-message alert-message notice">
	<p><?=t("Permissions for this area currently override those of the page.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToPagePermissions()"><?=t('Revert to Page Permissions')?></a>
	</div>

<? } ?>

<?=Loader::element('permission/help');?>

<? $cat = PermissionKeyCategory::getByHandle('area');?>

<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>">
<table class="ccm-permission-grid">

<?
$permissions = PermissionKey::getList('area');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($a);

?>
	<tr>

	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><? if ($enablePermissions) { ?><a dialog-title="<?=tc('PermissionKeyName', $pk->getPermissionKeyName())?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><? } ?><?=tc('PermissionKeyName', $pk->getPermissionKeyName())?><? if ($enablePermissions) { ?></a><? } ?></strong></td>
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
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn primary ccm-button-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<? } ?>

</div>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>&atask=set_advanced_permissions&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
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

ccm_revertToPagePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_page_permissions")?>&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_refreshAreaPermissions();
	});
}

ccm_setAreaPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_page_permissions")?>&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_refreshAreaPermissions();
	});
}

ccm_refreshAreaPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?atask=groups&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}


</script>