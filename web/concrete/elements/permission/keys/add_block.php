<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<? $btl = new BlockTypeList();
$blockTypes = $btl->get();
?>
<? $form = Loader::helper('form'); ?>

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<? if (count($included) > 0) { ?>

<h4><?=t('Who can add what?')?></h4>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="form-group">
	<label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
	<?=$form->select('blockTypesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Block Types'), 'C' => t('Custom')), $assignment->getBlockTypesAllowedPermission())?>
	<div class="inputs-list" <? if ($assignment->getBlockTypesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($blockTypes as $bt) { ?>
			<div class="checkbox"><label><input type="checkbox" name="btIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$bt->getBlockTypeID()?>" <? if (in_array($bt->getBlockTypeID(), $assignment->getBlockTypesAllowedArray())) { ?> checked="checked" <? } ?> /> <?=t($bt->getBlockTypeName())?></label></div>
		<? } ?>
	</div>
</div>

<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t add what?')?></h3>

<? foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="form-group">
    <label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
	<?=$form->select('blockTypesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Block Types'), 'C' => t('Custom')), $assignment->getBlockTypesAllowedPermission())?>
	<div class="inputs-list" <? if ($assignment->getBlockTypesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($blockTypes as $bt) { ?>
        <div class="checkbox"><label><input type="checkbox" name="btIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$bt->getBlockTypeID()?>" <? if (in_array($bt->getBlockTypeID(), $assignment->getBlockTypesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=t($bt->getBlockTypeName())?></span></label></div>
		<? } ?>
	</div>
</div>



<? }

} ?>

<? } else {  ?>
	<p><?=t('No users or groups selected.')?></p>
<? } ?>

<script type="text/javascript">
$(function() {
	$("#ccm-tab-content-custom-options select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('div.inputs-list').show();
		} else {
			$(this).parent().find('div.inputs-list').hide();
		}
	});
});
</script>