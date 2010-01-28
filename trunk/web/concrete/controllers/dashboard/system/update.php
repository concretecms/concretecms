<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('update');
class DashboardSystemUpdateController extends Controller { 	 
	
	function view() {  
		$upd = new Update();
		$updates = $upd->getLocalAvailableUpdates();
		
		$this->set('updates', $update);
	}
	
}
