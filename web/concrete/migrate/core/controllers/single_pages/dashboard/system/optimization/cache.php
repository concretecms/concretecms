<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_System_Optimization_Cache extends DashboardPageController {
	
	public $helpers = array('form'); 
	
	public function view(){
	}

	public function update_cache() {
		if ($this->token->validate("update_cache")) {
			if ($this->isPost()) {
				$u = new User();
				$eca = $this->post('ENABLE_BLOCK_CACHE') == 1 ? 1 : 0; 
				$eoc = $this->post('ENABLE_OVERRIDE_CACHE') == 1 ? 1 : 0; 
				$eac = $this->post('ENABLE_ASSET_CACHE') == 1 ? 1 : 0; 
				Cache::flush();
				Config::save('ENABLE_BLOCK_CACHE', $eca);
				Config::save('ENABLE_ASSET_CACHE', $eac);
				Config::save('ENABLE_OVERRIDE_CACHE', $eoc);
				Config::save('FULL_PAGE_CACHE_GLOBAL', $this->post('FULL_PAGE_CACHE_GLOBAL'));
				Config::save('FULL_PAGE_CACHE_LIFETIME', $this->post('FULL_PAGE_CACHE_LIFETIME'));
				Config::save('FULL_PAGE_CACHE_LIFETIME_CUSTOM', $this->post('FULL_PAGE_CACHE_LIFETIME_CUSTOM'));				
				$this->redirect('/dashboard/system/optimization/cache', 'cache_updated');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	public function cache_updated() {
		$this->set('message', t('Cache settings saved.'));	
		$this->view();
	}
	
	
	
	
	
}
