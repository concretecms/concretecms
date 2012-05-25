<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?

$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();

?>
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
	<?=$form->select('fileTypesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All File Types'), 'C' => t('Custom')), $assignment->getFileTypesAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getFileTypesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
	<? foreach($extensions as $ext) {
		$checked = ($assignment->getFileTypesAllowedPermission() == 1 || ($assignment->getFileTypesAllowedPermission() == 'C' && in_array($ext, $assignment->getFileTypesAllowedArray())));
		?>
			<li><label><input type="checkbox" name="extensionInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ext?>" <? if ($checked) { ?> checked="checked" <? } ?> /> <span><?=$ext?></span></label></li>
		<? } ?>
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
	<?=$form->select('fileTypesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No File Types'), 'C' => t('Custom')), $assignment->getFileTypesAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getFileTypesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
	<? foreach($extensions as $ext) {
		$checked = in_array($ext, $assignment->getFileTypesAllowedArray());
		?>
			<li><label><input type="checkbox" name="extensionExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ext?>" <? if ($checked) { ?> checked="checked" <? } ?> /> <span><?=$ext?></span></label></li>
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