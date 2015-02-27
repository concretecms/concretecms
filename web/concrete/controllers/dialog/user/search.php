<?php
namespace Concrete\Controller\Dialog\User;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Loader;
class Search extends BackendInterfaceController {

	protected $viewPath = '/dialogs/user/search';

	protected function canAccess() {
		$tp = Loader::helper('concrete/user');
		return $tp->canAccessUserSearchInterface();
	}

	public function view() {
		$cnt = new \Concrete\Controller\Search\Users();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
        $this->requireAsset('select2');
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

