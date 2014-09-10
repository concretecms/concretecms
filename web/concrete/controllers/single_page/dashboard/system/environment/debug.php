<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Debug extends DashboardPageController {

	public function view() {

		$debug_level = Config::get('concrete.debug.level');
		$this->set('debug_level', $debug_level);
	}

	public function update_debug() {
		if ($this->token->validate("update_debug")) {
			if ($this->isPost()) {
				Config::save('concrete.debug.level', $this->post('debug_level'));
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
