<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionKey->getAssignmentList(); ?>
<? $excluded = $permissionKey->getAssignmentList(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<? $themes = PageTheme::getList(); ?>
<? $form = Loader::helper('form'); ?>

<form id="ccm-page-permissions-add-subpage-form" onsubmit="return false" method="post" action="<?=$permissionKey->getPermissionKeyToolsURL()?>">

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<div class="well clearfix">

<? if (count($included) > 0) { ?>

<h3><?=t('Who can set what?')?></h3>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('themesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Themes'), 'C' => t('Custom')), $assignment->getThemesAllowedPermission())?><br/><br/>
	<ul class="theme-list inputs-list" <? if ($assignment->getThemesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($themes as $ct) { ?>
			<li><label><input type="checkbox" name="ptIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getThemeID()?>" <? if (in_array($ct->getThemeID(), $assignment->getThemesAllowedArray()) || $assignment->getThemesAllowedPermission() == 'A') { ?> checked="checked" <? } ?> /> <span><?=$ct->getThemeName()?></span></label></li>
		<? } ?>
	</ul>
	</div>
</div>


<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t set what?')?></h3>

<? foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('themesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Themes'), 'C' => t('Custom')), $assignment->getThemesAllowedPermission())?><br/><br/>
	<ul class="theme-list inputs-list" <? if ($assignment->getThemesAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($themes as $ct) { ?>
			<li><label><input type="checkbox" name="ptIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getThemeID()?>" <? if (in_array($ct->getThemeID(), $assignment->getThemesAllowedArray()) || $assignment->getThemesAllowedPermission() == 'N') { ?> checked="checked" <? } ?> /> <span><?=$ct->getThemeName()?></span></label></li>
		<? } ?>
	</ul>
	</div>
</div>



<? }

} ?>

<input type="submit" class="btn primary ccm-button-right" onclick="$('#ccm-page-permissions-add-subpage-form').submit()" value="<?=t('Update Custom Settings')?>" />
</div>

<? } ?>

</form>

<script type="text/javascript">
$(function() {
	$("#ccm-page-permissions-add-subpage-form select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('ul.theme-list').show();
		} else {
			$(this).parent().find('ul.theme-list').hide();
		}
	});
	
	$("#ccm-page-permissions-add-subpage-form").ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}
	});
});
</script>