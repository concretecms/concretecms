<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
if (is_object($composer) && $composer->getComposerTargetTypeID() == $this->getComposerTargetTypeID()) {
	$ps = Loader::helper('form/page_selector');
	print $ps->selectPage('cParentID');
}