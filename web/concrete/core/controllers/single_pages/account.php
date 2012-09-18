<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Account extends Controller {

	public $helpers = array('html', 'form', 'text'); 

	public function on_start(){
		if (!defined('ENABLE_USER_PROFILES') || !ENABLE_USER_PROFILES) {
			$this->render("/page_not_found");
		}
		$this->error = Loader::helper('validation/error');
		$this->addHeaderItem(Loader::helper('html')->css('ccm.account.css'));
		$this->set('vt', Loader::helper('validation/token'));
		$this->set('av', Loader::helper('concrete/avatar'));
		
		$c = Page::getCurrentPage();
		if ($c->getCollectionPath() == '/account') {
			$this->redirect('/account/profile/public');
		}
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}

	


}