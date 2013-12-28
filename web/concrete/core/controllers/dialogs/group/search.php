<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Group_Search extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/group/search';

	protected function canAccess() {
		$tp = new TaskPermission();
		return $tp->canAccessGroupSearch();
	}

	public function view() {
		$cnt = new SearchGroupsController();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->set('result', $result);
		$this->set('searchController', $cnt);
		$this->set('tree', GroupTree::get());
		$this->requireAsset('core/groups');
	}

}

