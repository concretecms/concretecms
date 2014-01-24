<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Page_Search_Customize extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/search/customize';
	protected $controllerActionPath = '/system/dialogs/page/search/customize';

	protected function canAccess() {
		$sh = Loader::helper('concrete/dashboard/sitemap');
		return $sh->canRead();
	}

	public function view() {
		$selectedAKIDs = array();
		$fldc = PageSearchColumnSet::getCurrent();
		$fldca = new PageSearchAvailableColumnSet();
		$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
		$list = CollectionAttributeKey::getList();
		$this->set('list', $list);
		$this->set('form', Loader::helper('form'));
		$this->set('fldca', $fldca);
		$this->set('fldc', $fldc);
		$this->set('type', 'pages');
	}

	public function submit() {
		if ($this->validateAction()) {
			$u = new User();
			$fdc = new PageSearchColumnSet();
			$fldca = new PageSearchAvailableColumnSet();
			foreach($_POST['column'] as $key) {
				$fdc->addColumn($fldca->getColumnByKey($key));
			}	
			$sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
			$fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
			$u->saveConfig('PAGE_LIST_DEFAULT_COLUMNS', serialize($fdc));

			$pageList = new PageList();
			$columns = PageSearchColumnSet::getCurrent();
			$col = $columns->getDefaultSortColumn();	
			$pageList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());

			$ilr = new PageSearchResult($columns, $pageList, URL::to('/system/search/pages/submit'));
			$r = new SearchResponse();
			$r->setMessage(t('Page search columns saved successfully.'));
			$r->setSearchResult($ilr);
			$r->outputJSON();
		}
	}

}

