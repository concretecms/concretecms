<?php
use Concrete\Core\Application\Service\Dashboard;
use Concrete\Core\Http\Service\Json;
use \Concrete\Core\Marketplace\RemoteItemList as MarketplaceRemoteItemList;

$dh = new Dashboard();
if ($dh->canRead()) {
    session_write_close();

    $js = new Json();

    $mri = new MarketplaceRemoteItemList();
    $mri->setItemsPerPage(5);
    $mri->setIncludeInstalledItems(false);
    $mri->setType('addons');
    $keywords = $_REQUEST['q'];
    $mri->filterByKeywords($keywords);
    $mri->execute();
    $items = $mri->getPage();

    $r = array();
    foreach ($items as $it) {
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
