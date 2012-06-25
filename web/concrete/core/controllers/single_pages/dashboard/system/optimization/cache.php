<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Optimization_Cache extends DashboardBaseController {
	
	public $helpers = array('form'); 
	
	public function view(){
	}

	public function update_cache() {
		if ($this->token->validate("update_cache")) {
			if ($this->isPost()) {
				$u = new User();
				$eca = $this->post('ENABLE_CACHE') == 1 ? 1 : 0; 
				$eoc = $this->post('ENABLE_OVERRIDE_CACHE') == 1 ? 1 : 0; 
				Cache::flush();
				$env = Environment::get();
				$env->clearOverrideCache();
				Config::save('ENABLE_CACHE', $eca);
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
