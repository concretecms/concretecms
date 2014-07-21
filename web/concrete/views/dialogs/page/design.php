<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::Helper('form');
$selectedThemeID = 0;
$selectedTemplateID = 0;
if (is_object($selectedTheme)) {
	$selectedThemeID = $selectedTheme->getThemeID();
}
if (is_object($selectedTemplate)) {
	$selectedTemplateID = $selectedTemplate->getPageTemplateID();
}
?>

<div class="ccm-ui">
<form method="post" data-dialog-form="design" action="<?=$controller->action('submit')?>">

    <input type="hidden" name="sitemap" value="1" />
    
	<div class="form-group">
		<?=$form->label('pTemplateID', t('Page Template'))?>
		<?=$form->select('pTemplateID', $templatesSelect, $selectedTemplateID)?>
	</div>

	<div class="form-group">
		<?=$form->label('pThemeID', t('Theme'))?>
		<?=$form->select('pThemeID', $themesSelect, $selectedThemeID)?>
	</div>



	<div class="dialog-buttons">
	<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
	</div>

</form>
</div>