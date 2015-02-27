<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Controller\Search\Pages as SearchPagesController;
use \Concrete\Core\Http\ResponseAssetGroup;
use Loader;

class Search extends DashboardPageController {
	
	public $helpers = array('form');
	
	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/sitemap');
		$cnt = new SearchPagesController();
		$cnt->search();
		$this->set('searchController', $cnt);
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->set('result', $result);
	}

}