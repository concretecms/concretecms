<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$cParentID = false;
if (is_object($page)) {
	$cParentID = $page->getPageDraftTargetParentPageID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
	$ps = Loader::helper('form/page_selector');
	print $ps->selectPage('cParentID', $cParentID);
}