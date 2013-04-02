<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
if (is_object($composer) && $composer->getComposerTargetTypeID() == $this->getComposerTargetTypeID()) {
	$configuredTarget = $composer->getComposerTargetObject();
	$cID = $configuredTarget->getParentPageID();
	$pc = Page::getByID($cID, 'ACTIVE');
?>
<span class="checkbox"><?=t('This page will be published beneath <a href="%s">%s</a>.', Loader::helper('navigation')->getLinkToCollection($pc), $pc->getCollectionName())?></label>
<? } ?>