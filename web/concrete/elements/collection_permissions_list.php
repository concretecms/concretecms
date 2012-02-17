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
foreach($permissions as $pk) { ?>
	<li><a dialog-width="500" dialog-height="380" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?=$c->getCollectionID()?>&ctask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a><br/><?=$pk->getPermissionKeyDescription()?></li>
<? } ?>
</ul>
</div>