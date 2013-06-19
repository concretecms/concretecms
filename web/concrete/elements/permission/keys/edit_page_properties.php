<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<? $attributes = CollectionAttributeKey::getList(); ?>
<? $form = Loader::helper('form'); ?>

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<? if (count($included) > 0) { ?>

<h3><?=t('Who can edit what?')?></h3>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('propertiesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <? if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($attributes as $ak) { ?>
			<li><label><input type="checkbox" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <? if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
		<? } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowEditName[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditName()) { ?>checked="checked" <? } ?> /> <span><?=t('Name')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDescription[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditDescription()) { ?>checked="checked" <? } ?> /> <span><?=t('Short Description')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUID[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditUserID()) { ?>checked="checked" <? } ?> /> <span><?=t('Owner')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDateTime[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditDateTime()) { ?>checked="checked" <? } ?> /> <span><?=t('Public Date/Time')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditPaths[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditPaths()) { ?>checked="checked" <? } ?> /> <span><?=t('Paths')?></span></label></li>
	</ul>

	</div>
</div>


<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t edit what?')?></h3>

<? foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">

	<?=$form->select('propertiesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Page Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <? if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($attributes as $ak) { ?>
			<li><label><input type="checkbox" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <? if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
		<? } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowEditNameExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditName()) { ?>checked="checked" <? } ?> /> <span><?=t('Name')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDescriptionExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditDescription()) { ?>checked="checked" <? } ?> /> <span><?=t('Short Description')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUIDExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditUserID()) { ?>checked="checked" <? } ?> /> <span><?=t('Owner')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDateTimeExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditDateTime()) { ?>checked="checked" <? } ?> /> <span><?=t('Public Date/Time')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditPathsExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditPaths()) { ?>checked="checked" <? } ?> /> <span><?=t('Paths')?></span></label></li>
	</ul>
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
			$(this).parent().find('ul.page-type-list').show();
		} else {
			$(this).parent().find('ul.page-type-list').hide();
		}
	});
	});
</script>