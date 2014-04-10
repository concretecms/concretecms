<?
namespace Concrete\Controller\SinglePage\Account;
use \Concrete\Core\Page\Controller\PageController;
class Members extends PageController {
	
	public function view() {
		$this->redirect('/account/members/directory');
	}

}