<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Sitemap_Search extends DashboardController {
	
	public $helpers = array('form');
	
	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/search');
		$cnt = new SearchPagesController();
		$cnt->search();

		$this->set('searchRequest', $cnt->getSearchRequest());

		$items = Loader::helper('json')->encode($cnt->getItems());
		$columns = Loader::helper('json')->encode($cnt->getColumns());
		$summary = Loader::helper('json')->encode($cnt->getSummary());
		$pagination = Loader::helper('json')->encode($cnt->getPagination());
		$fields = Loader::helper('json')->encode($cnt->getFields());
		
		$this->addFooterItem("<script type=\"text/javascript\">$(function() { $('div[data-search=pages]').concreteAjaxSearch({fields: " . $fields . ", pagination: " . $pagination . ", summary: " . $summary . ", items: " . $items . ", columns: " . $columns . "}); });</script>");

	}

}