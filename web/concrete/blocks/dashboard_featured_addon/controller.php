<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardFeaturedAddonBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;

		protected $btIsInternal = true;		
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 100;
		
		public function getBlockTypeDescription() {
			return t("Features an add-on from concrete5.org.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Featured Add-On");
		}
		
		public function view() {
			Loader::model('marketplace_remote_item');
			$mri = new MarketplaceRemoteItemList();
			$mri->sortBy('recommended');
			$mri->setItemsPerPage(1);
			$mri->setType('addons');
			$mri->execute();
			$items = $mri->getPage();
			if (is_object($items[0])) {
				$this->set('remoteItem', $items[0]);
			}
		}
		
	}