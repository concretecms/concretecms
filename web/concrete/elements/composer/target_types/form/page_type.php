<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
if (is_object($composer) && $composer->getComposerTargetTypeID() == $this->getComposerTargetTypeID()) {
	$configuredTarget = $composer->getComposerTargetObject();
	$pl = new PageList();
	$pl->sortByName();
	$pl->filterByCollectionTypeID($configuredTarget->getCollectionTypeID());
	$pl->sortByName();
	$pages = $pl->get();
	$options = array();
	foreach($pages as $p) {
		$options[$p->getCollectionID()] = $p->getCollectionName();
	}
	print $form->select('cParentID', $options);	

}