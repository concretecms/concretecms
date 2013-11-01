<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}
session_write_close();

session_write_close();

$keywords = $_REQUEST['q'];
Loader::model('page_list');
$pl = new PageList();
$pl->filterByName($keywords);
if (PERMISSIONS_MODEL != 'simple') {
	$pl->setViewPagePermissionKeyHandle('view_page_in_sitemap');
}
$pl->ignoreAliases();
$pl->sortBy('cID', 'asc');
$pl->setItemsPerPage(5);
$pages = $pl->getPage();
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