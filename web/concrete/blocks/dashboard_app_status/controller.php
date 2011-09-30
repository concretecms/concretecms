<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardAppStatusBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;

		protected $btIsInternal = true;		
		
		public function getBlockTypeDescription() {
			return t("Displays update and welcome back information on your dashboard.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard App Status");
		}
		
		public function view() {
			Loader::block('form');
			$this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
			$this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions(date('Y-m-d')));
		}
		
	}