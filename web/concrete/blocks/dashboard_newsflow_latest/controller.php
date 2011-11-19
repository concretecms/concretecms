<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardNewsflowLatestBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btIsInternal = true;		
		
		public function getBlockTypeDescription() {
			return t("Grabs the latest edition from concrete5.org.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Newsflow Latest");
		}
		
		public function view() {
			Loader::library('newsflow');
			$ni = Newsflow::getEditionByPath('/newsflow');
			if (is_object($ni)) { 
				$this->set('editionTitle', $ni->getTitle());
				$this->set('editionDescription', $ni->getDescription());
				$this->set('editionDate', $ni->getDate());
				$this->set('editionID', $ni->getID());
			} else {
			
			}
		}
		
	}