<?php
namespace Concrete\Controller\Backend\Marketplace;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\Service\Dashboard;
use Concrete\Core\Marketplace\RemoteItemList;

class Search extends UserInterface
{
    public function view()
    {
        session_write_close();

        $mri = new RemoteItemList();
        $mri->setItemsPerPage(5);
        $mri->setIncludeInstalledItems(false);
        $mri->filterByCompatibility(1);
        $mri->setType('addons');
        $keywords = $_REQUEST['q'];
        $mri->filterByKeywords($keywords);
        $mri->execute();
        $items = $mri->getPage();

        $r = array();
        foreach ($items as $it) {
            $obj = new \stdClass();
            $obj->mpID = $it->getMarketplaceItemID();
            $obj->name = $it->getName();
            $obj->img = $it->getRemoteIconURL();
            $obj->href = $it->getRemoteURL();
            $r[] = $obj;
        }

        echo json_encode($r);
        exit;
    }

    public function canAccess()
    {
        $dh = new Dashboard();

        return $dh->canRead();
    }
}
