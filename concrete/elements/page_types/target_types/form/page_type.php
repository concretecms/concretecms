<?php

defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$cParentID = false;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();

    if ($configuredTarget->getSelectorFormFactor() == 'sitemap_in_page') {
        $siteMapParentID = HOME_CID;
        if ($configuredTarget->getStartingPointPageID()) {
            $siteMapParentID = $configuredTarget->getStartingPointPageID();
        }
        $ps = Loader::helper('form/page_selector');
        echo $ps->selectFromSitemap('cParentID', $cParentID, $siteMapParentID, array('ptID' => $configuredTarget->getPageTypeID()));
    } else {
        $pl = new PageList();
        $pl->sortByName();
        $pl->filterByPageTypeID($configuredTarget->getPageTypeID());
        $pl->sortByName();
        $pages = $pl->get();
        if (count($pages) > 1) {
            $navigation = \Core::make('helper/navigation');
            $options = array();
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
