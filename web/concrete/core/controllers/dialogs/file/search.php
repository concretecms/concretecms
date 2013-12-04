<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_File_Search extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/file/search';

	protected function canAccess() {
		$cp = FilePermissions::getGlobal();
		if ((!$cp->canAddFile()) && (!$cp->canSearchFiles())) {
			return false;
		}
		return true;
	}

	public function view() {
		$cnt = new SearchFilesController();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

