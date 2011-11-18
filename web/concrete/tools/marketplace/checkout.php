<?  defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if ($tp->canInstallPackages()) { 
	Loader::library('marketplace');
	$mi = Marketplace::getInstance();
	Loader::model('marketplace_remote_item');
	$mp = MarketplaceRemoteItem::getByID($_REQUEST['mpID']);
	print '<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/jquery.postmessage.js"></script>';
	print $mi->getMarketplacePurchaseFrame($mp, '100%', '100%');
} else {
	print t('You do not have permission to connect to the marketplace.');
}