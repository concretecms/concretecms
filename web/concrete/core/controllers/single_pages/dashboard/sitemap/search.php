<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Sitemap_Search extends DashboardController {
	
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