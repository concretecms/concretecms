<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardFeaturedAddonBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btIsInternal = true;		

		public function getBlockTypeDescription() {
			return t("Features an add-on from concrete5.org.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Featured Add-On");
		}
		
		
		
	}