<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Area\SubArea;

?>
<div class="ccm-ui">
	<?php
$enablePermissions = false;
if ($a instanceof SubArea && (!$a->overrideCollectionPermissions())) {
    ?>

	<div class="alert alert-info">
	<p>
	<?=t("The following area permissions are inherited from a parent area. ")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_setAreaPermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
		
<?php 
} elseif ($a->getAreaCollectionInheritID() != $c->getCollectionID() && $a->getAreaCollectionInheritID() > 0) {
    $pc = $c->getPermissionsCollectionObject();
    $areac = Page::getByID($a->getAreaCollectionInheritID());
    ?>
		

		<div class="alert alert-info">
		<p>
		<?php if ($areac->isMasterCollection()) {
    ?>
			<?php $ptName = $areac->getPageTypeName();
    ?>
			<?=t("The following area permissions are inherited from an area set in <strong>%s</strong> defaults.", $ptName)?>
		<?php 
} else {
    ?>
			<?=t("The following area permissions are inherited from an area set on ")?>
			<a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$areac->getCollectionID()?>"><?=$areac->getCollectionName()?></a>. 
		<?php 
}
    ?>
		</p>
		<br/>
		<a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_setAreaPermissionsToOverride()"><?=t('Override Permissions')?></a>
		</div>
		
<?php 
} elseif (!$a->overrideCollectionPermissions()) {
    ?>

	<div class="alert alert-info">
	<p>
	<?=t("The following area permissions are inherited from the page's permissions.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_setAreaPermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<?php 
} else {
    $enablePermissions = true;
    ?>

	<div class="alert alert-info">
	<p><?=t("Permissions for this area currently override those of the page.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_revertToPagePermissions()"><?=t('Revert to Page Permissions')?></a>
	</div>

<?php 
} ?>

<?=Loader::element('permission/help');?>

<?php $cat = PermissionKeyCategory::getByHandle('area');?>

<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>">
<table class="ccm-permission-grid table table-striped">

<?php
$permissions = PermissionKey::getList('area');
foreach ($permissions as $pk) {
    $pk->setPermissionObject($a);

    ?>
	<tr>

	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><?php if ($enablePermissions) {
    ?><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?php 
}
    ?><?=$pk->getPermissionKeyDisplayName()?><?php if ($enablePermissions) {
    ?></a><?php 
}
    ?></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <?php if ($enablePermissions) {
    ?>class="ccm-permission-grid-cell"<?php 
}
    ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<?php 
} ?>

<?php if ($enablePermissions) {
    ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<?php 
} ?>

</table>
</form>

<?php if ($enablePermissions) {
    ?>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary pull-right"><?=t('Save')?> <i class="fa fa-ok-sign icon-white"></i></button>
</div>
<?php 
} ?>

</div>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>&duplicate=' + dupe + '&atask=set_advanced_permissions&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: true,
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