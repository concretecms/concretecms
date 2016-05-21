<?php

defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$cParentID = false;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();

    $ps = Loader::helper('form/page_selector');
    if ($configuredTarget->getSelectorFormFactor() == 'sitemap_in_page') {
        $siteMapParentID = HOME_CID;
        if ($configuredTarget->getStartingPointPageID()) {
            $siteMapParentID = $configuredTarget->getStartingPointPageID();
        }
        echo $ps->selectFromSitemap('cParentID', $cParentID, $siteMapParentID);
    } else {
        echo $ps->selectPage('cParentID', $cParentID);
    }
}
