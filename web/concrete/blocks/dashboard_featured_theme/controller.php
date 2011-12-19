<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardFeaturedThemeBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btCacheBlockOutputLifetime = 7200;

		protected $btIsInternal = true;		
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 100;
		
		public function getBlockTypeDescription() {
			return t("Features a theme from concrete5.org.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Featured Theme");
		}
		
		public function view() {
			Loader::model('marketplace_remote_item');
			$mri = new MarketplaceRemoteItemList();
			$mri->sortBy('recommended');
			$mri->setItemsPerPage(1);
			$mri->setType('themes');
			$mri->execute();
			$items = $mri->getPage();
			if (is_object($items[0])) {
				$this->set('remoteItem', $items[0]);
			}
		}
		
	}