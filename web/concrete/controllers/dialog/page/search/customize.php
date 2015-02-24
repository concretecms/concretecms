<?php
namespace Concrete\Controller\Dialog\Page\Search;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use \Concrete\Core\Page\Search\ColumnSet\ColumnSet as PageSearchColumnSet;
use \Concrete\Core\Page\Search\ColumnSet\Available as PageSearchAvailableColumnSet;
use CollectionAttributeKey;
use \Concrete\Core\Page\Search\Result\Result as PageSearchResult;
use \Concrete\Core\Search\Response as SearchResponse;
use Loader;
use User;
use PageList;
use URL;

class Customize extends BackendInterfaceController {

	protected $viewPath = '/dialogs/search/customize';

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
			$pageList->sanitizedSortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());

			$ilr = new PageSearchResult($columns, $pageList, URL::to('/ccm/system/search/pages/submit'));
			$r = new SearchResponse();
			$r->setMessage(t('Page search columns saved successfully.'));
			$r->setSearchResult($ilr);
			$r->outputJSON();
		}
	}

}

