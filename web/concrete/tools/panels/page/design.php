<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
$pt = $c->getPageTypeObject();
if (is_object($pt)) {
	$_templates = $pt->getPageTypePageTemplateObjects();
} else {
	$_templates = PageTemplate::getList();
}

$pTemplateID = $c->getPageTemplateID();
$templates = array();
if ($pTemplateID) {
	$selectedTemplate = PageTemplate::getByID($pTemplateID);
	$templates[] = $selectedTemplate;
}

foreach($_templates as $tmp) {
	if (!in_array($tmp, $templates)) {
		$templates[] = $tmp;
	}
}

if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canViewPageVersions()) { ?>

		<section id="ccm-panel-page-design">
			<header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="glyphicon glyphicon-chevron-left"></span></a> <?=t('Design')?></header>

			<div class="ccm-panel-content-inner">

			<? if ($cp->canEditPageTemplate() && !$c->isGeneratedCollection()) { ?>
				<div class="list-group">
					<div class="list-group-item list-group-item-header"><?=t('Page Type')?></div>
					<?
					foreach($templates as $tmp) { ?>
						<label class="list-group-item"><input type="checkbox" /> <?=$tmp->getPageTemplateName()?></label>
					<? } ?>
				</div>

			<? } ?>

			</div>

		</section>


	<? }
}
?>