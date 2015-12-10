<?php

defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/concrete/dashboard/sitemap');
if (!$dh->canRead()) {
    die(t("Access Denied."));
}

/*
if (isset($_REQUEST['selectedPageID'])) {
    if (strstr($_REQUEST['selectedPageID'], ',')) {
        $sanitizedPageID = preg_replace('/[^0-9,]/', '', $_REQUEST['selectedPageID']);
        $sanitizedPageID = preg_replace('/\s/', '', $sanitizedPageID);
    } else {
        $sanitizedPageID = intval($_REQUEST['selectedPageID']);
    }
    $dh->setSelectedPageID($sanitizedPageID);
}

if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'save_sitemap_display_mode') {
    $u = new User();
    $u->saveConfig('SITEMAP_OVERLAY_DISPLAY_MODE', $_REQUEST['display_mode']);
    exit;
}
*/

if ($_REQUEST['displayNodePagination']) {
    $dh->setDisplayNodePagination(true);
} else {
    $dh->setDisplayNodePagination(false);
}

if ($_GET['includeSystemPages']) {
    $dh->setIncludeSystemPages(true);
} else {
    $dh->setIncludeSystemPages(false);
}

$cParentID = (isset($_REQUEST['cParentID'])) ? $_REQUEST['cParentID'] : 0;
if (isset($_REQUEST['displaySingleLevel']) && $_REQUEST['displaySingleLevel']) {
    $c = Page::getByID($cParentID);
    $parent = Page::getByID($c->getCollectionParentID());
    if (is_object($parent) && !$parent->isError()) {
        $n = $dh->getNode($parent->getCollectionID());
        $n->iconHTML = '<i class="fa fa-angle-double-up"></i>';
        $n->icon = true;
        $n->displaySingleLevel = true;

        $p = $dh->getNode($cParentID);
        $p->children = $dh->getSubNodes($cParentID);
        $n->children = array($p);
    } else {
        $n = $dh->getNode($cParentID);
        $n->children = $dh->getSubNodes($cParentID);
    }
    $nodes[] = $n;
} else {
    if (isset($_COOKIE['ConcreteSitemap-expand'])) {
        $openNodeArray = explode(',', str_replace('_', '', $_COOKIE['ConcreteSitemap-expand']));
        if (is_array($openNodeArray)) {
            $dh->setExpandedNodes($openNodeArray);
        }
    }
    $nodes = $dh->getSubNodes($cParentID);
}
print Core::make('helper/json')->encode($nodes);
