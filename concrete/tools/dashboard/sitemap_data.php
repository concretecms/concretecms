<?php

defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/concrete/dashboard/sitemap');
if (!$dh->canRead()) {
    die(t("Access Denied."));
}

if (isset($_REQUEST['displayNodePagination']) && $_REQUEST['displayNodePagination']) {
    $dh->setDisplayNodePagination(true);
} else {
    $dh->setDisplayNodePagination(false);
}

$cParentID = (isset($_REQUEST['cParentID'])) ? $_REQUEST['cParentID'] : 0;
if (isset($_REQUEST['displaySingleLevel']) && $_REQUEST['displaySingleLevel']) {
    $c = Page::getByID($cParentID);
    $parent = Page::getByID($c->getCollectionParentID());
    if (is_object($parent) && !$parent->isError()) {
        $n = $dh->getNode($parent->getCollectionID());
        $n->icon = 'fa fa-angle-double-up';
        $n->expanded = true;
        $n->displaySingleLevel = true;

        $p = $dh->getNode($cParentID);
        $p->expanded = true;
        $p->children = $dh->getSubNodes($cParentID);
        $n->children = array($p);
    } else {
        $n = $dh->getNode($cParentID);
        $n->children = $dh->getSubNodes($cParentID);
    }
    $nodes[] = $n;
    echo json_encode([
        'children' => $nodes,
    ]);
} else {

    $provider = \Core::make('Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider');
    $formatter = new \Concrete\Core\Application\UserInterface\Sitemap\JsonFormatter($provider);
    print json_encode($formatter);
}

