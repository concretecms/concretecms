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
		<?=t("The following area permissions are inherited from an area set on ")?>
		<a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$areac->getCollectionID()?>"><?=$areac->getCollectionName()?></a>. 
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

<table class="ccm-permission-grid">

<?
$permissions = PermissionKey::getList('area');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($a);

?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><? if ($enablePermissions) { ?><a dialog-width="500" dialog-height="430" dialog-on-destroy="ccm_refreshAreaPermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>"  href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?arHandle=<?=$a->getAreaHandle()?>&cID=<?=$c->getCollectionID()?>&atask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><? } ?><?=$pk->getPermissionKeyName()?><? if ($enablePermissions) { ?></a><? } ?></strong></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>
</div>

<script type="text/javascript">
ccm_revertToPagePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("revert_to_page_permissions")?>&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_refreshAreaPermissions();
	});
}

ccm_setAreaPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("override_page_permissions")?>&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
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