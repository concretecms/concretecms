<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var Concrete\Core\Page\Page $target
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Page\Type\PublishTarget\Configuration\Configuration $configuration
 */

$form = app('helper/form');
$cParentID = false;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}

$relevantPage = null;
$tree = null;

if (is_object($control->getPageObject())) {
    $relevantPage = $control->getPageObject();
} else if ($control->getTargetParentPageID()) {
    $relevantPage = \Page::getByID($control->getTargetParentPageID());
    $tree = $relevantPage->getSiteTreeObject();
}

if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();

    $ps = app('helper/form/page_selector');
    if ($configuredTarget->getSelectorFormFactor() == 'sitemap_in_page') {
        if ($configuredTarget->getStartingPointPageID()) {
            $siteMapParentID = $configuredTarget->getStartingPointPageID();
        } else {
            $siteMapParentID = Page::getHomePageID($relevantPage);
        }
        echo $ps->selectFromSitemap('cParentID', $cParentID, $siteMapParentID, $tree);
    } else {
        echo $ps->selectPage('cParentID', $cParentID);
    }
}
