<? defined('C5_EXECUTE') or die("Access Denied.");

$dh = Loader::helper('concrete/dashboard');
if ($dh->canRead()) {
	session_write_close();
	
	$js = Loader::helper('json');
	Loader::model('marketplace_remote_item');

	$mri = new MarketplaceRemoteItemList();
	$mri->setItemsPerPage(5);
	$mri->setIncludeInstalledItems(false);
	$mri->setType('addons');
	$keywords = $_REQUEST['q'];
	$mri->filterByKeywords($keywords);
	$mri->execute();
	$items = $mri->getPage();	

	$r = array();	
	foreach($items as $it) {
		$obj = new stdClass;
		$obj->mpID = $it->getMarketplaceItemID();
		$obj->name = $it->getName();
		$obj->img = $it->getRemoteIconURL();
		$obj->href = $it->getRemoteURL();
		$r[] = $obj;
	}
	print $js->encode($r);
	exit;
}