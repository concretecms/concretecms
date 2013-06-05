<?  defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('marketplace');
Loader::model('marketplace_remote_item');

$js = Loader::helper('json');
$mi = Marketplace::getInstance();

$obj = new stdClass;
$obj->isConnected = $mi->isConnected();
$obj->connectionError = $mi->getConnectionError();
if ($mi->isConnected() && isset($_REQUEST['mpID'])) {
	// we also perform the "does the user need to buy it?" query here to save some requests
	$mr = MarketplaceRemoteItem::getByID($_REQUEST['mpID']);
	if (is_object($mr)) {
		$obj->purchaseRequired = $mr->purchaseRequired();
		$obj->remoteURL = $mr->getRemoteURL();
		// if purchase is NOT required then we also try and add a license
		// don't worry - this is also verified on the server
		if (!$mr->purchaseRequired()) {
			$mr->enableFreeLicense();
		}
	}
}
print $js->encode($obj);
exit;