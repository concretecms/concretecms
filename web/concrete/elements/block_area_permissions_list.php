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
<ul>
<?
$permissions = PermissionKey::getList('area');
foreach($permissions as $pk) { 
	$pk->setAreaObject($a);

?>
	<li><? if ($enablePermissions) { ?><a dialog-width="500" dialog-height="380" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?arHandle=<?=$a->getAreaHandle()?>&cID=<?=$c->getCollectionID()?>&atask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><? } ?><?=$pk->getPermissionKeyName()?><? if ($enablePermissions) { ?></a><? } ?><br/><?=$pk->getPermissionKeyDescription()?>
	<br/><br/>
	<?
	$included = $pk->getAssignmentList(AreaPermissionKey::ACCESS_TYPE_INCLUDE);
	$excluded = $pk->getAssignmentList(AreaPermissionKey::ACCESS_TYPE_EXCLUDE);
	
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
	
	
	<div><?=t('Included: %s', $includedStr)?></div>
	<div><?=t('Excluded: %s', $excludedStr)?></div>
	<br/>
	</li>
<? } ?>
</ul>
</div>

<script type="text/javascript">
ccm_revertToPagePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("revert_to_page_permissions")?>&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?atask=groups&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_setAreaPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("override_page_permissions")?>&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?atask=groups&arHandle=<?=urlencode($a->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

</script>