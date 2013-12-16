<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Page_Search extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/page/search';

	protected function canAccess() {
		$sh = Loader::helper('concrete/dashboard/sitemap');
		return $sh->canRead();
	}

	public function view() {
		$cnt = new SearchPagesController();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

