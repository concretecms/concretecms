<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
	<? 
	global $c;
	global $a;

$enablePermissions = false;
if (!$b->overrideAreaPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?=t("Permissions for this block are currently dependent on the area containing this block.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_setBlockPermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<? } else { 
	$enablePermissions = true;
	?>

	<div class="block-message alert-message notice">
	<p><?=t("Permissions for this block currently override those of the area and page.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToAreaPermissions()"><?=t('Revert to Area Permissions')?></a>
	</div>

<? } ?>


<?=Loader::element('permission/help');?>

<table class="ccm-permission-grid">

<?
$permissions = PermissionKey::getList('block');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($b);

?>
<tr>
	<td class="ccm-permission-grid-name"><strong><? if ($enablePermissions) { ?><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshBlockPermissions()" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?bID=<?=$b->getBlockID()?>&arHandle=<?=$b->getAreaHandle()?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>&cID=<?=$c->getCollectionID()?>&btask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><? } ?><?=$pk->getPermissionKeyName()?><? if ($enablePermissions) { ?></a><? } ?></strong></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>




</div>

<script type="text/javascript">
ccm_revertToAreaPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("revert_to_area_permissions")?>&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_refreshBlockPermissions();
	});
}

ccm_setBlockPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("override_area_permissions")?>&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_refreshBlockPermissions();
	});
}

ccm_refreshBlockPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=groups&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}

</script>