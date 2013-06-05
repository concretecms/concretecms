<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Environment_Debug extends DashboardBaseController {
	
	public function view() {
		
		$debug_level = Config::get('SITE_DEBUG_LEVEL');
		$this->set('debug_level', $debug_level);		
	}
	
	public function update_debug() {
		if ($this->token->validate("update_debug")) {
			if ($this->isPost()) {
				Config::save('SITE_DEBUG_LEVEL', $this->post('debug_level'));
				$this->redirect('/dashboard/system/environment/debug', 'debug_saved');

			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	public function debug_saved(){
		$this->set('message', t('Debug configuration saved.'));	
		$this->view();
	}
		
}