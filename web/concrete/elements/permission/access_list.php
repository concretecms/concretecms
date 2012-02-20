<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (!$accessType) {
	$accessType = PermissionKey::ACCESS_TYPE_INCLUDE;
}
?>
<table class="zebra-striped">
<tr>
	<th colspan="3"><?=t('User/Group')?></th>
</tr>
<? foreach($list as $pa) {
	$pae = $pa->getAccessEntityObject(); 
	?>
<tr>
	<td width="100%"><?=$pae->getAccessEntityLabel()?></td>
	<td><a href="javascript:void(0)" onclick=""><img src="<?=ASSETS_URL_IMAGES?>/icons/clock<? if (is_object($pa->getPermissionDurationObject())) { ?>_active<? } ?>.png" width="16" height="16" /><a/></td>
	<td><a href="javascript:void(0)" onclick="ccm_deleteAccessEntityAssignment(<?=$pae->getAccessEntityID()?>)"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /><a/></td>
<? } ?>
</table>

<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?accessType=<?=$accessType?>" class="btn small dialog-launch" dialog-width="500" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>"><?=t('Add')?></a>
