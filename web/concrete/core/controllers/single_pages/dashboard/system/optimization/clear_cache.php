<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Optimization_ClearCache extends DashboardBaseController {
	
	public $helpers = array('form'); 
	
	public function view(){
	}
	
	public function do_clear() {
		if ($this->token->validate("clear_cache")) {
			if ($this->isPost()) {
				Cache::flush();
				$env = Environment::get();
				$env->clearOverrideCache();
				$this->redirect('/dashboard/system/optimization/clear_cache', 'cache_cleared');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function cache_cleared() {
		$this->set('message', t('Cached files removed.'));	
		$this->view();
	}
	
	
}
