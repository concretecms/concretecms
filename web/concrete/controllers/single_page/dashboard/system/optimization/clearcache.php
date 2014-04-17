<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Cache;

class Clearcache extends DashboardPageController {
	
	public $helpers = array('form'); 
	
	public function view(){
	}
	
	public function do_clear() {
		if ($this->token->validate("clear_cache")) {
			if ($this->isPost()) {
				Cache::flush();
				$this->redirect('/dashboard/system/optimization/clearcache', 'cache_cleared');
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
