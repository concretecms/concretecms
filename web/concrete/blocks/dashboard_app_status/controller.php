<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class DashboardAppStatusBlockController extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btCacheBlockOutputLifetime = 86400; // check every day

		protected $btIsInternal = true;		
		
		public function getBlockTypeDescription() {
			return t("Displays update and welcome back information on your dashboard.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard App Status");
		}
		
		public function view() {
			Loader::library('update');
			$this->set('latest_version', Update::getLatestAvailableVersionNumber());
			$tp = new TaskPermission();
			$updates = 0;
			$local = array();
			$remote = array();
			if ($tp->canInstallPackages()) { 
				$local = Package::getLocalUpgradeablePackages();
				$remote = Package::getRemotelyUpgradeablePackages();
			}
			
			// now we strip out any dupes for the total
			$updates = 0;
			$localHandles = array();
			foreach($local as $_pkg) {
				$updates++;
				$localHandles[] = $_pkg->getPackageHandle();
			}
			foreach($remote as $_pkg) {
				if (!in_array($_pkg->getPackageHandle(), $localHandles)) {
					$updates++;
				}
			}
			$this->set('updates', $updates);
		}
		
	}