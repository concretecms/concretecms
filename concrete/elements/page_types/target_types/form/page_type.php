<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Page $target
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Page\Type\PublishTarget\Configuration\Configuration $configuration
 */

$form = app('helper/form');
$cParentID = false;
$tree = null;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}

$relevantPage = null;

if (is_object($control->getPageObject())) {
    $relevantPage = $control->getPageObject();
} elseif ($control->getTargetParentPageID()) {
    $relevantPage = \Page::getByID($control->getTargetParentPageID());
}

if (is_object($relevantPage) && !$relevantPage->isError()) {
    $site = $relevantPage->getSite();
    $tree = $relevantPage->getSiteTreeObject();
}

if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();

    if ($configuredTarget->getSelectorFormFactor() == 'sitemap_in_page') {
        if ($configuredTarget->getStartingPointPageID()) {
            $siteMapParentID = $configuredTarget->getStartingPointPageID();
        } else {
            $siteMapParentID = Page::getHomePageID($relevantPage);
        }

        $ps = app('helper/form/page_selector');
        $args = ['ptID' => $configuredTarget->getPageTypeID()];
        echo $ps->selectFromSitemap('cParentID', $cParentID, $siteMapParentID, $tree, $args);
    } else {
        $pl = new PageList();
        $pl->sortByName();
        $pl->filterByPageTypeID($configuredTarget->getPageTypeID());
        $pl->sortByName();
        if (is_object($site)) {
            $pl->filterBySite($site);
        }
        $pages = $pl->getResults();

        if (count($pages) > 1) {
            $navigation = $ps = app('helper/navigation');
            $options = [];
            foreach ($pages as $p) {
                $pp = new Permissions($p);
                if ($pp->canAddSubCollection($pagetype)) {
					$label = '';
					$trail = $navigation->getTrailToCollection($p);
					if (is_array($trail)) {
						$trail = array_reverse($trail);
						for ($i = 0; $i < count($trail); $i++) {
							$label .= $trail[$i]->getCollectionName() . ' &gt; ';
						}
					}
					$label .= $p->getCollectionName();
					$options[$p->getCollectionID()] = $label;
                }
            }
            echo $form->select('cParentID', $options, $cParentID);
        } elseif (count($pages) == 1) {
            $p = $pages[0];
            echo $form->hidden('cParentID', $p->getCollectionID());
            echo t('This page will be published beneath "%s."', $p->getCollectionName());
        }
    }
}
