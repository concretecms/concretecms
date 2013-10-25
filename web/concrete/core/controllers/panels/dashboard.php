<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Dashboard extends PanelController {

	protected $viewPath = '/system/panels/dashboard';

	protected function canViewPanel() {
		$dh = Loader::helper('concrete/dashboard');
		return $dh->canRead();
	}

	public function view() {
		$c = Page::getByPath('/dashboard');
		$children = $c->getCollectionChildrenArray(true);
		$this->set('children', $children);
	}

}

