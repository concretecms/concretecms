<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Marketplace\RemoteItem;
use TaskPermission;
use Marketplace;
use Concrete\Core\Marketplace\RemoteItemList as MarketplaceRemoteItemList;

/**
 * Abstract controller for extending Concrete CMS through the Dashboard.
 * 
 */

abstract class MarketplaceDashboardPageController extends DashboardPageController
{
    abstract public function getMarketplaceType();
    abstract public function getMarketplaceDefaultHeading();

    public function view_detail($mpID = null)
    {
        $this->setThemeViewTemplate('marketplace.php');
        $this->set('type', $this->getMarketplaceType());
        $this->set('heading', $this->getMarketplaceDefaultHeading());

        $tp = new TaskPermission();
        $mi = Marketplace::getInstance();

        if ($mi->isConnected() && $tp->canInstallPackages()) {
            $mpID = intval($mpID);
            $item = RemoteItem::getByID($mpID);
            if (is_object($item)) {
                if (
                ($item->getMarketplaceItemType() == 'theme' && $this->getMarketplaceType() == 'themes') ||
                ($item->getMarketplaceItemType() == 'add_on' && $this->getMarketplaceType() == 'addons')) {
                    $this->set('item', $item);
                } else {
                    $this->redirect('/dashboard/extend/connect');
                }
            } else {
                throw new \Exception(t('Invalid marketplace item object.'));
            }
        } else {
            $this->redirect('/dashboard/extend/connect');
        }
    }

    public function view()
    {
        $this->setThemeViewTemplate('marketplace.php');
        $this->set('type', $this->getMarketplaceType());
        $this->set('heading', $this->getMarketplaceDefaultHeading());

        $tp = new TaskPermission();
        $mi = Marketplace::getInstance();

        if ($mi->isConnected() && $tp->canInstallPackages()) {
            $mri = new MarketplaceRemoteItemList();
            $mri->setItemsPerPage(9);
            $sets = MarketplaceRemoteItemList::getItemSets($this->getMarketplaceType());

            $setsel = array('' => t('All Items'), 'FEATURED' => t('Featured Items'));
            $req = $this->request->query;
            if (is_array($sets)) {
                foreach ($sets as $s) {
                    $setsel[$s->getMarketplaceRemoteSetID()] = $s->getMarketplaceRemoteSetName();
                    if ($req->has('marketplaceRemoteItemSetID') && $req->get('marketplaceRemoteItemSetID') ==
                        $s->getMarketplaceRemoteSetID()) {
                        $this->set('heading', $s->getMarketplaceRemoteSetName());
                    }
                }
            }

            switch ($this->request->query->get('ccm_order_by')) {
                case 'rating':
                case 'skill_level':
                case 'recent':
                    $mri->sortBy($this->request->query->get('ccm_order_by'));
                    $this->set('sort', $this->request->query->get('ccm_order_by'));
                    break;
                case 'price':
                    $mri->sortBy('price_low');
                    $this->set('sort', 'price');
                    break;
                default:
                    $mri->sortBy('popularity');
                    $this->set('sort', 'popularity');
                    break;
            }

            $mri->setIncludeInstalledItems(false);

            $mri->filterByCompatibility(1);
            $requestKeywords = $_REQUEST['keywords'] ?? '';
            if ($requestKeywords) {
                $keywords = h($requestKeywords);
                $mri->filterByKeywords($keywords);
                $this->set('keywords', $keywords);
            }

            $set = $_REQUEST['marketplaceRemoteItemSetID'] ?? '';
            if ($set) {
                $mri->filterBySet($set);
            }

            $mri->setType($this->getMarketplaceType());
            $mri->execute();

            $items = $mri->getPage();

            $this->set('pagination', $mri->getPagination());
            $this->set('items', $items);
            $this->set('sets', $setsel);
            $this->set('list', $mri);
        } else {
            $this->redirect('/dashboard/extend/connect');
        }
    }
}
