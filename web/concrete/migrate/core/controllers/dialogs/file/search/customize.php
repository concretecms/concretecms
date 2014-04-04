<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_File_Search_Customize extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/search/customize';
	protected $controllerActionPath = '/system/dialogs/file/search/customize';

	protected function canAccess() {
		$sh = Loader::helper('concrete/dashboard/sitemap');
		return $sh->canRead();
	}

	public function view() {
		$selectedAKIDs = array();
		$fldc = FileSearchColumnSet::getCurrent();
		$fldca = new FileSearchAvailableColumnSet();
		$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
		$list = FileAttributeKey::getList();
		$this->set('list', $list);
		$this->set('form', Loader::helper('form'));
		$this->set('fldca', $fldca);
		$this->set('fldc', $fldc);
		$this->set('type', 'files');
	}

	public function submit() {
		if ($this->validateAction()) {
			$u = new User();
			$fdc = new FileSearchColumnSet();
			$fldca = new FileSearchAvailableColumnSet();
			foreach($_POST['column'] as $key) {
				$fdc->addColumn($fldca->getColumnByKey($key));
			}	
			$sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
			$fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
			$u->saveConfig('FILE_LIST_DEFAULT_COLUMNS', serialize($fdc));

			$fileList = new FileList();
			$columns = FileSearchColumnSet::getCurrent();
			$col = $columns->getDefaultSortColumn();	
			$fileList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());

			$ilr = new FileSearchResult($columns, $fileList, URL::to('/system/search/files/submit'));
			$r = new SearchResponse();
			$r->setMessage(t('File search columns saved successfully.'));
			$r->setSearchResult($ilr);
			$r->outputJSON();
		}
	}

}

