<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Dashboard extends FrontendEditPageController {

	protected $viewPath = '/system/panels/dashboard';

	protected function canAccess() {
		$dh = Loader::helper('concrete/dashboard');
		return $dh->canRead();
	}

	public function view() {
		$c = Page::getByPath('/dashboard');
		$children = $c->getCollectionChildrenArray(true);
		$this->set('children', $children);

		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$this->set('ui', $ui);
	}

}

