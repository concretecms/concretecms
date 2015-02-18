<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$cParentID = false;
if (is_object($target)) {
	$cParentID = $target->getCollectionID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
	$configuredTarget = $pagetype->getPageTypePublishTargetObject();
	$pl = new PageList();
	$pl->sortByName();
	$pl->filterByPageTypeID($configuredTarget->getPageTypeID());
	$pl->sortByName();
	$pages = $pl->get();
    if (count($pages) > 1) {
        $options = array();
        foreach($pages as $p) {
            $options[$p->getCollectionID()] = $p->getCollectionName();
        }
        print $form->select('cParentID', $options, $cParentID);
    } else if (count($pages) == 1) {
        $p = $pages[0];
        print $form->hidden('cParentID', $p->getCollectionID());
        print t('This page will be published beneath "%s."', $p->getCollectionName());
    }
}