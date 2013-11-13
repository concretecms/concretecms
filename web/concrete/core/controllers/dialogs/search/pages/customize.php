<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Search_Pages_Customize extends FrontendEditController {

	protected $viewPath = '/system/dialogs/search/pages/customize';

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
			$pageList->resetSearchRequest();
			$r = new PageEditResponse();
			$r->setMessage(t('Page search columns saved successfully.'));
			Loader::helper('ajax')->sendResult($r);
		}
	}

}

