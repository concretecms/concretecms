<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Dashboard extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/dashboard';

	protected function canAccess() {
		$dh = Loader::helper('concrete/dashboard');
		return $dh->canRead();
	}

	public function view() {
		if ($this->request->get('tab')) {
			Cookie::set('panels/dashboard/tab', $this->request->get('tab'));
			$tab = $this->request->get('tab');
		} else {
			$tab = Cookie::get('panels/dashboard/tab');
		}

		$nav = array();
		if ($tab != 'favorites') {
			$c = Page::getByPath('/dashboard');
			$bt = BlockType::getByHandle('autonav');
			$bt->controller->displayPages = 'custom';
			$bt->controller->displaySystemPages = true;
			$bt->controller->displayPagesCID = $c->getCollectionID();
			$bt->controller->orderBy = 'display_asc';
			$bt->controller->displaySubPages = 'relevant'; 
			$bt->controller->displaySubPageLevels = 'all';
			$this->set('nav', $bt);
		} else {
			$dh = Loader::helper('concrete/dashboard');
			$qn = ConcreteDashboardMenu::getMine();
			foreach($qn->getItems() as $path) {
				$c = Page::getByPath($path, 'ACTIVE');
				if (is_object($c) && !$c->isError()) {
					$nav[] = $c;
				}
			}
			$this->set('nav', $nav);
		}

		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$this->set('ui', $ui);
		$this->set('tab', $tab);

	}

}

