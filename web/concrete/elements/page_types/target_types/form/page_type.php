<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$cParentID = false;
if (is_object($page)) {
	$cParentID = $page->getPageDraftTargetParentPageID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
	$configuredTarget = $pagetype->getPageTypePublishTargetObject();
	$pl = new PageList();
	$pl->sortByName();
	$pl->filterByPageTypeID($configuredTarget->getPageTypeID());
	$pl->sortByName();
	$pages = $pl->get();
	$options = array();
	foreach($pages as $p) {
		$options[$p->getCollectionID()] = $p->getCollectionName();
	}
	print $form->select('cParentID', $options, $cParentID);	

}