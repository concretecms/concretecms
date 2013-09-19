<?
defined('C5_EXECUTE') or die("Access Denied.");
$templates = array();
$composer = $set->getComposerObject();
foreach($composer->getComposerPageTemplateObjects() as $template) {
	$templates[$template->getPageTemplateID()] = $template->getPageTemplateName();
}
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls" data-composer-field="page_template">
		<?=$form->select('cmpPageTemplateID', $templates, $control->getComposerControlDraftValue())?>
	</div>
</div>