<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardNewsflowLatestBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputLifetime = 7200;
		protected $btTable = 'btDashboardNewsflowLatest';
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btIsInternal = true;
		
		public function getBlockTypeDescription() {
			return t("Grabs the latest newsflow data from concrete5.org.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Newsflow Latest");
		}
		
		public function view() {
			Loader::library('newsflow');
			// get the latest data as well
			$slots = Newsflow::getSlotContents();
			$this->set('slot', $slots[$this->slot]);
			
			// this is kind of a hack
			if ($this->slot == 'C') { 
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
		
	}