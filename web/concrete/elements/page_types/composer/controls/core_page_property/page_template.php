<?
defined('C5_EXECUTE') or die("Access Denied.");
$templates = array();
$pagetype = $set->getPagetypeObject();
foreach($pagetype->getPageTypePageTemplateObjects() as $template) {
	$templates[$template->getPageTemplateID()] = $template->getPageTemplateName();
}
$ptComposerPageTemplateID = $control->getPageTypeComposerControlDraftValue();
if (!$ptComposerPageTemplateID) {
	$ptComposerPageTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
}
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls" data-composer-field="page_template">
		<?=$form->select('ptComposerPageTemplateID', $templates, $ptComposerPageTemplateID)?>
	</div>
</div>