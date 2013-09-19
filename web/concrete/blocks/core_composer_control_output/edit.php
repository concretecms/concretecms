<?
	defined('C5_EXECUTE') or die("Access Denied.");

	$c = Page::getCurrentPage();
	// retrieve all block composer controls attached to this page template.
	$pt = PageTemplate::getByID($c->getPageTemplateID());
	$cmp = Composer::getByDefaultsPage($c);
	$controls = ComposerOutputControl::getList($cmp, $pt);
	$values = array();
	foreach($controls as $control) {
		$fls = ComposerFormLayoutSetControl::getByID($control->getComposerFormLayoutSetControlID());
		$cc = $fls->getComposerControlObject();
		$values[$control->getComposerOutputControlID()] = $cc->getComposerControlName();
	}
	$form = Loader::helper('form');
?>
<div class="form-group">
	<label for="cmpOutputControlID" class="control-label"><?=t('Control')?></label>
	<?=$form->select('cmpOutputControlID', $values, $cmpOutputControlID)?>
</div>