<table class="ccm-permission-access-list">
<tr>
	<th colspan="3">
		<div style="position: relative">
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity" dialog-width="500" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>" class="ccm-advanced-search-add-field ccm-add-access-entity"><span class="ccm-menu-icon ccm-icon-view"></span><?=t('Add')?></a>
		

	<?=t('User/Group')?>
	</div>
	</th>
</tr>
<? if (count($list) > 0) { ?>

<? foreach($list as $pa) {
	$pae = $pa->getAccessEntityObject(); 
	$pdID = 0;
	if (is_object($pa->getPermissionDurationObject())) { 
		$pdID = $pa->getPermissionDurationObject()->getPermissionDurationID();
	}
	
	?>
<tr>
	<td width="100%"><?=$pae->getAccessEntityLabel()?></td>
	<td><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?peID=<?=$pae->getAccessEntityID()?>&pdID=<?=$pdID?>" dialog-width="500" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>" class="ccm-add-access-entity"><img src="<?=ASSETS_URL_IMAGES?>/icons/clock<? if (is_object($pa->getPermissionDurationObject())) { ?>_active<? } ?>.png" width="16" height="16" /><a/></td>
	<td><a href="javascript:void(0)" onclick="ccm_deleteAccessEntityAssignment(<?=$pae->getAccessEntityID()?>)"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /><a/></td>
</tr>

<? } ?>

<? } else { ?>
	<tr>
	<td colspan="3" class="ccm-workflow-access-entities-none"><?=t('None')?></td>
	</tr>
<? } ?>

</table>

<script type="text/javascript">
$(function() {
$('.ccm-add-access-entity').dialog();
});
</script>
