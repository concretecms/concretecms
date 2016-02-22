<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $included = $permissionAccess->getAccessListItems(); ?>
<?php $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?php

$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();

?>
<?php $form = Loader::helper('form'); ?>

<?php if (count($included) > 0 || count($excluded) > 0) {
    ?>

<?php if (count($included) > 0) {
    ?>

<h3><?=t('Who can add what?')?></h3>

<?php foreach ($included as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('fileTypesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All File Types'), 'C' => t('Custom')), $assignment->getFileTypesAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <?php if ($assignment->getFileTypesAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
	<?php foreach ($extensions as $ext) {
    $checked = ($assignment->getFileTypesAllowedPermission() == 1 || ($assignment->getFileTypesAllowedPermission() == 'C' && in_array($ext, $assignment->getFileTypesAllowedArray())));
    ?>
			<li><label><input type="checkbox" name="extensionInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ext?>" <?php if ($checked) {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$ext?></span></label></li>
		<?php 
}
    ?>
	</ul>
	</div>
</div>

<?php 
}
}
    ?>


<?php if (count($excluded) > 0) {
    ?>

<h3><?=t('Who can\'t add what?')?></h3>

<?php foreach ($excluded as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('fileTypesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No File Types'), 'C' => t('Custom')), $assignment->getFileTypesAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <?php if ($assignment->getFileTypesAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
	<?php foreach ($extensions as $ext) {
    $checked = in_array($ext, $assignment->getFileTypesAllowedArray());
    ?>
			<li><label><input type="checkbox" name="extensionExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ext?>" <?php if ($checked) {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$ext?></span></label></li>
		<?php 
}
    ?>
	</ul>
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
			$(this).parent().find('ul.inputs-list').show();
		} else {
			$(this).parent().find('ul.inputs-list').hide();
		}
	});
});
</script>