<?php
namespace Concrete\Core\Page\Controller;
use Config;
use Loader;
use Page;
class AccountPageController extends PageController {

	public $helpers = array('html', 'form', 'text');

	public function on_start(){
		if (!Config::get('concrete.user.profiles_enabled')) {
            $this->render('/page_not_found');
		}
		$this->error = Loader::helper('validation/error');
		$this->set('valt', Loader::helper('validation/token'));
		$this->set('av', Loader::helper('concrete/avatar'));

		$c = Page::getCurrentPage();
		if ($c->getCollectionPath() == '/account') {
			$this->redirect('/account/profile/public_profile');
		}
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}



}
