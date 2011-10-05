<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardFormSummaryBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btIsInternal = true;		
		
		public function getBlockTypeDescription() {
			return t("Displays the current number of form submissions in the Dashboard.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Form Submissions Summary");
		}
		
		public function view() {
			Loader::block('form');
			$this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
			$this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions(date('Y-m-d')));
		}
		
	}