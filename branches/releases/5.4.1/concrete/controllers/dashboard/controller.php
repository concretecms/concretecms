<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardController extends Controller {

	public function view() {
		$this->set('latest_version', Config::get('APP_VERSION_LATEST'));
		Loader::model('dashboard/homepage');
		$dh = new DashboardHomepageView();
		$modules = $dh->getModules();
		$this->set('dh', $dh);
		$this->set('modules', $modules);		
		$html = Loader::helper('html');
		$this->addHeaderItem($html->javascript('swfobject.js'));
	}
	
	public function module($module = null, $task = null) {
		Loader::model('dashboard/homepage');
		$dh = new DashboardHomepageView();

		$mod = $dh->getByHandle($module);
		if ($mod->pkgID > 0) {
			$pkg = Package::getByID($mod->pkgID);
			$class = Loader::dashboardModuleController($mod->dbhModule, $pkg);
		} else {
			$class = Loader::dashboardModuleController($mod->dbhModule);
		}
		
		$args = func_get_args();

		array_shift($args);
		array_shift($args); // no that's not a misprint
		
		if (method_exists($class, $task)) {
			try {
				$resp = call_user_func_array(array($class, $task), $args);
				if ($resp) {
					$this->set('message', $resp);
				}
			} catch(Exception $e) {
				$this->set('error', $e);
			}
		}
				
		print $this->view();
	}
	
}

?>