<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $included = $permissionAccess->getAccessListItems(); ?>
<?php $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?php $pageTypes = PageType::getList(); ?>
<?php $form = Loader::helper('form'); ?>

<?php if (count($included) > 0 || count($excluded) > 0) { ?>

<?php if (count($included) > 0) { ?>

<h3><?=t('Who can add what?')?></h3>

<?php foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('pageTypesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Page Types'), 'C' => t('Custom')), $assignment->getPageTypesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <?php if ($assignment->getPageTypesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
		<?php foreach($pageTypes as $ct) { ?>
			<li><label><input type="checkbox" name="ptIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getPageTypeID()?>" <?php if (in_array($ct->getPageTypeID(), $assignment->getPageTypesAllowedArray())) { ?> checked="checked" <?php } ?> /> <span><?=$ct->getPageTypeDisplayName()?></span></label></li>
		<?php } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowExternalLinksIncluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowExternalLinks()) { ?>checked="checked" <?php } ?> /> <span><?=t('Allow External Links')?></span></label></li>
	</ul>

	</div>
</div>


<?php }

} ?>


<?php if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t add what?')?></h3>

<?php foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('pageTypesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Page Types'), 'C' => t('Custom')), $assignment->getPageTypesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <?php if ($assignment->getPageTypesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
		<?php foreach($pageTypes as $ct) { ?>
			<li><label><input type="checkbox" name="ptIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getPageTypeID()?>" <?php if (in_array($ct->getPageTypeID(), $assignment->getPageTypesAllowedArray())) { ?> checked="checked" <?php } ?> /> <span><?=$ct->getPageTypeDisplayName()?></span></label></li>
		<?php } ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowExternalLinksExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowExternalLinks()) { ?>checked="checked" <?php } ?> /> <span><?=t('Allow External Links')?></span></label></li>
	</ul>
	</div>
</div>



<?php }

} ?>

<?php } else {  ?>
	<p><?=t('No users or groups selected.')?></p>
<?php } ?>

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