<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?
Loader::model('attribute/categories/user');
$attribs = UserAttributeKey::getList();
?>
<? $form = Loader::helper('form'); ?>

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<? if (count($included) > 0) { ?>

<h3><?=t('Who can view what?')?></h3>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('viewAttributesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($attribs as $ak) { ?>
			<li><label><input type="checkbox" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <? if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
		<? } ?>
	</ul>
	</div>
</div>

<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t view what?')?></h3>

<? foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('viewAttributesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($attribs as $ak) { ?>
			<li><label><input type="checkbox" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <? if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
		<? } ?>
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
			$(this).parent().find('ul.inputs-list').show();
		} else {
			$(this).parent().find('ul.inputs-list').hide();
		}
	});
});
</script>