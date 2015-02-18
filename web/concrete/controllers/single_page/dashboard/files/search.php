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
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$v = View::getInstance();
        $v->requireAsset('core/file-manager');
        $v->requireAsset('core/imageeditor');
		$this->addFooterItem("<script type=\"text/javascript\">$(function() { $('div[data-search=files]').concreteFileManager({result: " . $result . "}); });</script>");
	}

}
