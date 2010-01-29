<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('update');
Loader::library('archive');

class UpdateArchive extends Archive {
	
	public function __construct() {
		parent::__construct();
		$this->targetDirectory = DIR_APP_UPDATES;
	}

	public function install($file) {
		parent::install($file, true);
	}
	
}

class DashboardSystemUpdateController extends Controller { 	 
	
	function view() {  
		$upd = new Update();
		$updates = $upd->getLocalAvailableUpdates();
		$remote = $upd->getApplicationUpdateInformation();
		$this->set('updates', $updates);
		if (MULTI_SITE == 0 && is_object($remote) && version_compare($remote->version, APP_VERSION, '>')) {
			$this->set('downloadableUpgradeAvailable', true);
			$this->set('update', $remote);
		} else {
			$this->set('downloadableUpgradeAvailable', false);
		}
	}
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function download_update() {
		if (MULTI_SITE == 1) {
			return false;
		}
		
		$ph = Loader::helper('package');
		$vt = Loader::helper('validation/token');
		if (!$vt->validate('download_update')) {
			$this->error->add($vt->getErrorMessage());
		}
		
		$remote = Update::getApplicationUpdateInformation();
		if (is_object($remote)) {
			// try to download
			$r = $ph->download_remote_package($remote->url);
			if (empty($r) || $r == Package::E_PACKAGE_DOWNLOAD) {
				$response = array(Package::E_PACKAGE_DOWNLOAD);
			} else if ($r == Package::E_PACKAGE_SAVE) {
				$response = array($r);
			}
			
			if (isset($response)) {
				$errors = Package::mapError($response);
				foreach($errors as $e) {
					$this->error->add($e);
				}
			}
			
			if (!$this->error->has()) {
				// the file exists in the right spot
				Loader::library('archive');
				$ar = new UpdateArchive();
				try {
					$ar->install($r);
				} catch(Exception $e) {
					$this->error->add($e->getMessage());
				}
					
			}
		} else {
			$this->error->add(t('Unable to retrieve update location.'));
		}
		$this->view();
	}
	
	public function do_update() {
		$updateVersion = $this->post('updateVersion');
		if (!$updateVersion) {
			$this->error->add(t('Invalid version'));
		} else {
			$upd = ApplicationUpdate::getByVersionNumber($updateVersion);
		}
		
		if (!is_object($upd)) {
			$this->error->add(t('Invalid version'));
		} else {
			if (version_compare($upd->getUpdateVersion(), APP_VERSION, '<=')) {
				$this->error->add(t('You may only apply updates with a greater version number than the version you are currently running.'));
			}
		}
		
		if (!$this->error->has()) {
			$resp = $upd->apply();
			if ($resp !== true) {
				switch($resp) {
					case ApplicationUpdate::E_UPDATE_WRITE_CONFIG:
						$this->error->add(t('Unable to write to config/site.php. You must make config/site.php writable in order to upgrade in this manner.'));
						break;
				}
			} else {
				$this->redirect(REL_DIR_FILES_TOOLS_REQUIRED . '/upgrade?source=dashboard_update');
			}
		}
		$this->view();

	}
}