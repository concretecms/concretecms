<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionAccess->getAccessListItems(); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<? $themes = PageTheme::getList(); ?>
<? $form = Loader::helper('form'); ?>

<? if (count($included) > 0 || count($excluded) > 0) { ?>

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
			<li><label><input type="checkbox" name="pThemeIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getThemeID()?>" <? if (in_array($ct->getThemeID(), $assignment->getThemesAllowedArray()) || $assignment->getThemesAllowedPermission() == 'A') { ?> checked="checked" <? } ?> /> <span><?=$ct->getThemeDisplayName()?></span></label></li>
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
			<li><label><input type="checkbox" name="pThemeIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ct->getThemeID()?>" <? if (in_array($ct->getThemeID(), $assignment->getThemesAllowedArray()) || $assignment->getThemesAllowedPermission() == 'N') { ?> checked="checked" <? } ?> /> <span><?=$ct->getThemeDisplayName()?></span></label></li>
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
			$(this).parent().find('ul.theme-list').show();
		} else {
			$(this).parent().find('ul.theme-list').hide();
		}
	});
});
</script>