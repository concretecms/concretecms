<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<? $pageTypes = PageType::getList(); ?>
<? $form = Loader::helper('form'); ?>

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<? if (count($included) > 0) { ?>

<h3><?=t('Who can add what?')?></h3>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('pageTypesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Page Types'), 'C' => t('Custom')), $assignment->getPageTypesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <? if ($assignment->getPageTypesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($pageTypes as $ct) { ?>
			<li><label><input type="checkbox" name="ptIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getPageTypeID()?>" <? if (in_array($ct->getPageTypeID(), $assignment->getPageTypesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=$ct->getPageTypeDisplayName()?></span></label></li>
		<? } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowExternalLinksIncluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowExternalLinks()) { ?>checked="checked" <? } ?> /> <span><?=t('Allow External Links')?></span></label></li>
	</ul>

	</div>
</div>


<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t add what?')?></h3>

<? foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('pageTypesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Page Types'), 'C' => t('Custom')), $assignment->getPageTypesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <? if ($assignment->getPageTypesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($pageTypes as $ct) { ?>
			<li><label><input type="checkbox" name="ptIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getPageTypeID()?>" <? if (in_array($ct->getPageTypeID(), $assignment->getPageTypesAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=$ct->getPageTypeDisplayName()?></span></label></li>
		<? } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowExternalLinksExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <? if ($assignment->allowExternalLinks()) { ?>checked="checked" <? } ?> /> <span><?=t('Allow External Links')?></span></label></li>
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