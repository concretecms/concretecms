<?
	defined('C5_EXECUTE') or die("Access Denied.");

	$c = Page::getCurrentPage();
	// retrieve all block controls attached to this page template.
	$pt = PageTemplate::getByID($c->getPageTemplateID());
	$ptt = PageType::getByDefaultsPage($c);
	$controls = PageTypeComposerOutputControl::getList($ptt, $pt);
	$values = array();
	foreach($controls as $control) {
		$fls = PageTypeComposerFormLayoutSetControl::getByID($control->getPageTypeComposerFormLayoutSetControlID());
		$cc = $fls->getPageTypeComposerControlObject();
		$values[$control->getPageTypeComposerOutputControlID()] = $cc->getPageTypeComposerControlName();
	}
	$form = Loader::helper('form');
?>
<div class="form-group">
	<label for="ptComposerOutputControlID" class="control-label"><?=t('Control')?></label>
	<?=$form->select('ptComposerOutputControlID', $values, $ptComposerOutputControlID)?>
</div>