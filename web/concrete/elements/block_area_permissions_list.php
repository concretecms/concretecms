<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<ul>
<?
$permissions = PermissionKey::getList('area');
foreach($permissions as $pk) { ?>
	<li><a dialog-width="500" dialog-height="380" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?arHandle=<?=$a->getAreaHandle()?>&cID=<?=$c->getCollectionID()?>&atask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a><br/><?=$pk->getPermissionKeyDescription()?></li>
<? } ?>
</ul>
</div>