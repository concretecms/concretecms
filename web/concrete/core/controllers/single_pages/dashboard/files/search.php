<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Files_Search extends DashboardController {

	public function view() {
		$cnt = new SearchFilesController();
		$cnt->search();
		$this->set('searchController', $cnt);
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->addFooterItem("<script type=\"text/javascript\">$(function() { $('div[data-search=files]').concreteAjaxSearch({result: " . $result . "}); });</script>");
	}

}