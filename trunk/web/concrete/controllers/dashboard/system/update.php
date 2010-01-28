<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('update');
class DashboardSystemUpdateController extends Controller { 	 
	
	function view() {  
		$upd = new Update();
		$updates = $upd->getLocalAvailableUpdates();		
		$this->set('updates', $updates);
	}
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
	}

	public function on_before_render() {
		$this->set('error', $this->error);
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
	}
}
