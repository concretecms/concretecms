<?

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardExtendAddonsController extends Controller {
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		Loader::library('marketplace');

	}
	
	public function view() {

		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			Loader::model('marketplace_remote_item');
			
			$mri = new MarketplaceRemoteItemList();
			$mri->setItemsPerPage(9);
			$sets = MarketplaceRemoteItemList::getItemSets('addons');
			
			$setsel = array('' => t('All Items'), 'FEATURED' => t('Featured Items'));
			if (is_array($sets)) {
				foreach($sets as $s) {
					$setsel[$s->getMarketplaceRemoteSetID()] = $s->getMarketplaceRemoteSetName();
				}
			}
			
			$mri->setIncludeInstalledItems(false);
			if (isset($_REQUEST['marketplaceRemoteItemSetID'])) {
				$set = $_REQUEST['marketplaceRemoteItemSetID'];
			}
	
			if (isset($_REQUEST['marketplaceRemoteItemKeywords'])) {
				$keywords = $_REQUEST['marketplaceRemoteItemKeywords'];
			}
			
			if ($keywords != '') {
				$mri->filterByKeywords($keywords);
			}
			
			if ($set == 'FEATURED') {
				$mri->filterByIsFeaturedRemotely(1);
			} else if ($set > 0) {
				$mri->filterBySet($set);
			}
			
			$mri->setType($what);
			$mri->execute();
			
			$items = $mri->getPage();
	
			$this->set('selectedSet', $set);
			$this->set('list', $mri);
			$this->set('items', $items);
			$this->set('form', Loader::helper('form'));
			$this->set('sets', $setsel);
			$this->set('type', $what);
		}
	}
	


}