<?
namespace Concrete\Controller\SinglePage\Account;
use \Concrete\Core\Page\Controller\AccountPageController;
class Profile extends AccountPageController {

	public function view() {
		$this->redirect('/account/profile/public');
	}
		
}