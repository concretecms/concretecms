<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
?>
<div class="ccm-ui">
<ul>
<?
$permissions = PermissionKey::getList('page');
foreach($permissions as $pk) { 
	$pk->setPageObject($c);
	?>
	<li><a dialog-width="500" dialog-height="380" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?=$c->getCollectionID()?>&ctask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a><br/><?=$pk->getPermissionKeyDescription()?>
	<br/><br/>
	<?
	$included = $pk->getAssignmentList(PagePermissionKey::ACCESS_TYPE_INCLUDE);
	$excluded = $pk->getAssignmentList(PagePermissionKey::ACCESS_TYPE_EXCLUDE);
	
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