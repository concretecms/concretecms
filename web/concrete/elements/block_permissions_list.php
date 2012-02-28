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
<table>
<?
$permissions = PermissionKey::getList('block');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($b);

?>
	<tr><td style="white-space: nowrap"><strong><? if ($enablePermissions) { ?><a dialog-width="500" dialog-height="380" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?bID=<?=$b->getBlockID()?>&arHandle=<?=$b->getAreaHandle()?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>&cID=<?=$c->getCollectionID()?>&btask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><? } ?><?=$pk->getPermissionKeyName()?><? if ($enablePermissions) { ?></a><? } ?></strong></td>
	<td width="100%">
	<?
	$included = $pk->getAssignmentList(BlockPermissionKey::ACCESS_TYPE_INCLUDE);
	$excluded = $pk->getAssignmentList(BlockPermissionKey::ACCESS_TYPE_EXCLUDE);
	
	$includedStr = t('None');
	$excludedStr = t('None');
	if (count($included) > 0) {
		$includedStr = '';
		for ($i = 0; $i < count($included); $i++) { 
			$as = $included[$i];
			$entity = $as->getAccessEntityObject();
			$includedStr .= $entity->getAccessEntityLabel();
			if ($i + 1 < count($included)) {
				$includedStr .= ', ';
			}
		}
	}
	if (count($excluded) > 0) {
		$excludedStr = '';
		for ($i = 0; $i < count($excluded); $i++) { 
			$as = $excluded[$i];
			$entity = $as->getAccessEntityObject();
			$excludedStr .= $entity->getAccessEntityLabel();
			if ($i + 1 < count($excluded)) {
				$excludedStr .= ', ';
			}
		}
	}
	
	?>
	
	
	<?=t('Included: %s.', $includedStr)?> <?=t('Excluded: %s', $excludedStr)?>
	</td>
</tr>
<? } ?>
</table>
</div>

<script type="text/javascript">
ccm_revertToAreaPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("revert_to_area_permissions")?>&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=groups&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_setBlockPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("override_area_permissions")?>&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=groups&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

</script>