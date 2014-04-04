<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_User_Search extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/user/search';

	protected function canAccess() {
		$tp = Loader::helper('concrete/user');
		return $tp->canAccessUserSearchInterface();
	}

	public function view() {
		$cnt = new SearchUsersController();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

