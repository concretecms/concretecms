<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Seo_BulkSeoTool extends Controller {
	
	public $helpers = array('form', 'concrete/interface');
	
	public function view() {
		$html = Loader::helper('html');
		$pageList = $this->getRequestedSearchResults();
		if (is_object($pageList)) {
			$pages = $pageList->getPage();
					
			$this->set('pageList', $pageList);		
			$this->set('pages', $pages);		
			$this->set('searchInstance', $searchInstance);
			$this->set('pagination', $pageList->getPagination());
		}
	}
	
	public function saveRecord() {
		$text = Loader::helper('text');
        $success = false;
        $success = 'success';
        $cID = $this->post('cID');
        $c = Page::getByID($cID);
        if (trim(sprintf(PAGE_TITLE_FORMAT, SITE, $c->getCollectionName())) != trim($this->post('meta_title')) && $this->post('meta_title')) {
        	 $c->setAttribute('meta_title',trim($this->post('meta_title')));
		}
		if (trim(htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET)) != trim($this->post('meta_description')) && $this->post('meta_description'))  {
        	$c->setAttribute('meta_description', trim($this->post('meta_description')));
		}
    	$c->setAttribute('meta_keywords',$this->post('meta_keywords'));
        $cHandle = $this->post('collection_handle');
        $c->update(array('cHandle'=>$cHandle));
        $c->rescanCollectionPath();
        $newPath = Page::getCollectionPathFromID($cID);
		$newHandle = $text->urlify($cHandle);
        $result = array('success'=>$success, 'cID'=>$cID, 'cHandle'=>$newHandle, 'newPath' => $newHandle);
        echo Loader::helper('json')->encode($result);
        exit;
    }
	
	public function getRequestedSearchResults() {
	
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if (!$dh->canRead()) {
			return false;
		}
		
		$pageList = new PageList();
		$pageList->ignoreAliases();
		$pageList->enableStickySearchRequest();
		
		if ($_REQUEST['submit_search']) {
			$pageList->resetSearchRequest();
		}

		$req = $pageList->getSearchRequest();
		$pageList->displayUnapprovedPages();

		$pageList->sortBy('cDateModified', 'desc');

		$columns = PageSearchColumnSet::getCurrent();
		$this->set('columns', $columns);
		
		$cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);
		
		if ($cvName != '') {
			$pageList->filterByName($cvName);
		}

		if ($req['cParentIDSearchField'] > 0) {
			if ($req['cParentAll'] == 1) {
				$pc = Page::getByID($req['cParentIDSearchField']);
				$cPath = $pc->getCollectionPath();
				$pageList->filterByPath($cPath);
			} else {
				$pageList->filterByParentID($req['cParentIDSearchField']);
			}
			$parentDialogOpen = 1;
		}

		$keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
		$pageList->filterByKeywords($keywords, true);

		if ($req['numResults']) {
			$pageList->setItemsPerPage($req['numResults']);
		}

		if ($req['ctID']) {
			$pageList->filterByCollectionTypeID($req['ctID']);
		}

		if ($_REQUEST['noKeywords'] == 1){
			$pageList->filter('CollectionSearchIndexAttributes.ak_meta_keywords', NULL ,'=');
			$this->set('keywordCheck', true);
			$parentDialogOpen = 1;
		}
		
		if ($_REQUEST['noDescription'] == 1){
			$pageList->filter('CollectionSearchIndexAttributes.ak_meta_description', NULL ,'=');
			$this->set('descCheck', true);
			$parentDialogOpen = 1;
		}
		
		$this->set('searchRequest', $req);
		$this->set('parentDialogOpen', $parentDialogOpen);
		
		return $pageList;
	}
}
	