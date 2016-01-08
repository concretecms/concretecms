<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Controller\Search\Files as SearchFilesController;
use View;
use Loader;
class Search extends DashboardPageController {

	public function view() {
		$cnt = new SearchFilesController();
		$cnt->search();
		$this->set('searchController', $cnt);
		$result = $cnt->getSearchResultObject();
		if (is_object($result)) {
			$result = Loader::helper('json')->encode($result->getJSONObject());
			$v = View::getInstance();
        	$v->requireAsset('core/file-manager');
        	$v->requireAsset('core/imageeditor');
			$this->addFooterItem("<script type=\"text/javascript\">$(function() { $('div[data-search=files]').concreteFileManager({result: " . $result . "}); });</script>");
		}
	}

}
