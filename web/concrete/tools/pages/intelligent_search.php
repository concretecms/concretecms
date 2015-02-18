<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}
session_write_close();

session_write_close();

$keywords = $_REQUEST['q'];
$pl = new PageList();
$pl->filterByName($keywords);
$pl->sortBy('cID', 'asc');
$pl->setItemsPerPage(5);
$pl->setPermissionsChecker(function($page) {
    $pp = new Permissions($page);
    return $pp->canViewPageInSitemap();
});
$pagination = $pl->getPagination();
$pages = $pagination->getCurrentPageResults();

$results = array();
$nh = Loader::helper('navigation');
foreach($pages as $c) {
	$obj = new stdClass;
	$obj->href = $nh->getLinkToCollection($c);
	$obj->cID = $c->getCollectionID();
	$obj->name = $c->getCollectionName();
	$results[] = $obj;
}

print Loader::helper("json")->encode($results);