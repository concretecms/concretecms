<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $included = $permissionAccess->getAccessListItems(); ?>
<?php $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?php $pageTypes = PageType::getList(false, $permissionAccess->getPermissionObject()->getSiteTreeObject()->getSiteType()); ?>
<?php $form = Loader::helper('form'); ?>

<?php if (count($included) > 0 || count($excluded) > 0) {
    ?>

<?php if (count($included) > 0) {
    ?>

<h4><?=t('Who can add what?')?></h4>

<?php foreach ($included as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="form-group">
	<label class="col-form-label"><?=$entity->getAccessEntityLabel()?></label>
	<?=$form->select('pageTypesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Page Types'), 'C' => t('Custom')), $assignment->getPageTypesAllowedPermission())?>
	<div class="page-type-list inputs-list m-sm-2" <?php if ($assignment->getPageTypesAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php
        $index = 1;
        foreach ($pageTypes as $ct) {
    ?>
			<div class="form-check"><input class="form-check-input" type="checkbox" id="ptIDInclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" name="ptIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getPageTypeID()?>" <?php if (in_array($ct->getPageTypeID(), $assignment->getPageTypesAllowedArray())) {
    ?> checked="checked" <?php 
}
    ?> /> <label for="ptIDInclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" class="form-check-label"><?=$ct->getPageTypeDisplayName()?></label></div>
		<?php
           $index++;
		}
    ?>
	</div>
	<div class="inputs-list mt-sm-4">
		<div class="form-check"><input type="checkbox"  class="form-check-input" id="allowExternalLinksIncluded[<?=$entity->getAccessEntityID()?>]" name="allowExternalLinksIncluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowExternalLinks()) {
    ?>checked="checked" <?php 
}
    ?> /> <label class="form-check-label" for="allowExternalLinksIncluded[<?=$entity->getAccessEntityID()?>]">
            <?=t('Allow External Links')?>
            </label>
        </div>
	</div>

</div>


<?php 
}
}
    ?>


<?php if (count($excluded) > 0) {
    ?>

<h4><?=t('Who can\'t add what?')?></h4>

<?php foreach ($excluded as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="form-group">
	<label class="col-form-label"><?=$entity->getAccessEntityLabel()?></label>
	<?=$form->select('pageTypesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Page Types'), 'C' => t('Custom')), $assignment->getPageTypesAllowedPermission())?>
	<div class="page-type-list inputs-list m-sm-2" <?php if ($assignment->getPageTypesAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php
        $index = 1;
        foreach ($pageTypes as $ct) {
    ?>
			<div class="form-check">
                <input class="form-check-input" id="ptIDExclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" type="checkbox" name="ptIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getPageTypeID()?>" <?php if (in_array($ct->getPageTypeID(), $assignment->getPageTypesAllowedArray())) {
    ?> checked="checked" <?php 
}
    ?> />       <label class="form-check-label" for="ptIDExclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>"><?=$ct->getPageTypeDisplayName()?></label>
            </div>
		<?php 
            $index++;
        }
    ?>
	</div>
	<div class="inputs-list mt-sm-4">
		<div class="form-check">
            <input class="form-check-input" type="checkbox" id="allowExternalLinksExcluded[<?=$entity->getAccessEntityID()?>]" name="allowExternalLinksExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowExternalLinks()) {
    ?>checked="checked" <?php 
}
    ?> />
            <label for="allowExternalLinksExcluded[<?=$entity->getAccessEntityID()?>]" class="form-check-label"> <?=t('Allow External Links')?></label>
        </div>
	</div>
</div>



<?php 
}
}
    ?>

<?php 
} else {
    ?>
	<p><?=t('No users or groups selected.')?></p>
<?php 
} ?>

<script type="text/javascript">
$(function() {
	$("#ccm-tab-content-custom-options select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('.page-type-list').show();
		} else {
			$(this).parent().find('.page-type-list').hide();
		}
	});
});
</script>