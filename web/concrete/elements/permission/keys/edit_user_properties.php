<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<? Loader::model("attribute/categories/user"); ?>
<? $attributes = UserAttributeKey::getList(); ?>
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
	<ul class="attribute-list inputs-list" <? if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($attributes as $ak) { ?>
			<li><label><input type="checkbox" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <? if ($assignment->getAttributesAllowedPermission() == 'A' || in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
		<? } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowEditUName[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditUserName()) { ?>checked="checked" <? } ?> /> <span><?=t('Username')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUEmail[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditEmail()) { ?>checked="checked" <? } ?> /> <span><?=t('Email Address')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUPassword[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditPassword()) { ?>checked="checked" <? } ?> /> <span><?=t('Password')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUAvatar[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditAvatar()) { ?>checked="checked" <? } ?> /> <span><?=t('Avatar')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUTimezone[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditTimezone()) { ?>checked="checked" <? } ?> /> <span><?=t('Timezone')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUDefaultLanguage[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditDefaultLanguage()) { ?>checked="checked" <? } ?> /> <span><?=t('Default Language')?></span></label></li>
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

	<?=$form->select('propertiesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="attribute-list inputs-list" <? if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($attributes as $ak) { ?>
			<li><label><input type="checkbox" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <? if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
		<? } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowEditUNameExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditUserName()) { ?>checked="checked" <? } ?> /> <span><?=t('Username')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUEmailExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditEmail()) { ?>checked="checked" <? } ?> /> <span><?=t('Email Address')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUPasswordExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditPassword()) { ?>checked="checked" <? } ?> /> <span><?=t('Password')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUAvatarExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditAvatar()) { ?>checked="checked" <? } ?> /> <span><?=t('Avatar')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUTimezoneExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditTimezone()) { ?>checked="checked" <? } ?> /> <span><?=t('Timezone')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUDefaultLanguageExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowEditDefaultLanguage()) { ?>checked="checked" <? } ?> /> <span><?=t('Default Language')?></span></label></li>
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
			$(this).parent().find('ul.attribute-list').show();
		} else {
			$(this).parent().find('ul.attribute-list').hide();
		}
	});
});
</script>