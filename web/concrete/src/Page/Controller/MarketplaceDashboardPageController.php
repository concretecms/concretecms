<?php
namespace Concrete\Core\Page\Controller;
use TaskPermission;
use Marketplace;
use Loader;
use \Concrete\Core\Marketplace\RemoteItemList as MarketplaceRemoteItemList;

abstract class MarketplaceDashboardPageController extends DashboardPageController
{

    abstract public function getMarketplaceType();
    abstract public function getMarketplaceDefaultHeading();

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
			$sets = MarketplaceRemoteItemList::getItemSets('themes');

			$setsel = array('' => t('All Items'), 'FEATURED' => t('Featured Items'));
            $req = $this->request->query;
			if (is_array($sets)) {
				foreach($sets as $s) {
					$setsel[$s->getMarketplaceRemoteSetID()] = $s->getMarketplaceRemoteSetName();
                    if ($req->has('marketplaceRemoteItemSetID') && $req->get('marketplaceRemoteItemSetID') ==
                        $s->getMarketplaceRemoteSetID()) {
                        $this->set('heading', $s->getMarketplaceRemoteSetName());
                    }
				}
			}

            switch($this->request->query->get('ccm_order_by')) {
                case 'recent':
                case 'rating':
                    $mri->sortBy($this->request->query->get('ccm_order_by'));
                    $this->set('sort', $this->request->query->get('ccm_order_by'));
                    break;
                case 'price':
                    $mri->sortBy('price_low');
                    $this->set('sort', 'price');
                    break;
                default:
                    $mri->sortBy('recommended');
                    $this->set('sort', 'recommended');
                    break;
            }

            $mri->setIncludeInstalledItems(false);
			if (isset($_REQUEST['marketplaceRemoteItemSetID'])) {
				$set = $_REQUEST['marketplaceRemoteItemSetID'];
			}

    		//$mri->filterByCompatibility(1);
			if (isset($_REQUEST['keywords']) && $_REQUEST['keywords']) {
				$keywords = h($_REQUEST['keywords']);
				$mri->filterByKeywords($keywords);
                $this->set('keywords', $keywords);
			}

			$mri->setType($this->getMarketplaceType());
			$mri->execute();

            $items = $mri->getPage();

            /*

			$mri->setIncludeInstalledItems(false);
			if (isset($_REQUEST['marketplaceRemoteItemSetID'])) {
				$set = $_REQUEST['marketplaceRemoteItemSetID'];
			}

			if (isset($_REQUEST['mpID'])) {
				$mri->filterByMarketplaceItemID($_REQUEST['mpID']);
			}

			if (isset($_REQUEST['marketplaceRemoteItemSortBy'])) {
				$this->set('selectedSort', Loader::helper('text')->entities($_REQUEST['marketplaceRemoteItemSortBy']));
				$mri->sortBy($_REQUEST['marketplaceRemoteItemSortBy']);
			} else {
				$mri->sortBy('recommended');
			}

			if (isset($_REQUEST['marketplaceIncludeOnlyCompatibleAddons']) && $_REQUEST['marketplaceIncludeOnlyCompatibleAddons'] == 1) {
				$mri->filterByCompatibility(1);
			}

			if (isset($_REQUEST['marketplaceRemoteItemKeywords']) && $_REQUEST['marketplaceRemoteItemKeywords']) {
				$keywords = $_REQUEST['marketplaceRemoteItemKeywords'];
				$sortBy = array('relevance' => t('Relevance')) + $sortBy;
			}

			if ($keywords != '') {
				$mri->filterByKeywords($keywords);
			}

			if ($set == 'FEATURED') {
				$mri->filterByIsFeaturedRemotely(1);
			} else if ($set > 0) {
				$mri->filterBySet($set);
			}

			$mri->setType('themes');
			$mri->execute();

			$items = $mri->getPage();

			$this->set('sortBy', $sortBy);
			$this->set('selectedSet', $set);
			$this->set('items', $items);
			$this->set('form', Loader::helper('form'));
			$this->set('pagination', $mri->getPagination());
			$this->set('type', $what);
            */

			$this->set('pagination', $mri->getPagination());
			$this->set('items', $items);
   			$this->set('sets', $setsel);
			$this->set('list', $mri);

		} else {
			$this->redirect('/dashboard/extend/connect');
		}
	}
}
