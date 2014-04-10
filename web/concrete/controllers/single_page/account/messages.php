<?
namespace Concrete\Controller\SinglePage\Account;
use \Concrete\Core\Page\Controller\AccountPageController;

class Message extends AccountPageController {
	
	public function view() {
		$this->redirect('/account/messages/inbox');
	}

}