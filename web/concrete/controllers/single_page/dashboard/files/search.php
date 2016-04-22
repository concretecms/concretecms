<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Controller\Element\Search\Files\Header;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Controller\Search\Files as SearchFilesController;
use View;
use Loader;

class Search extends DashboardPageController
{
    public function view()
    {

        $header = new Header();
        $this->set('headerMenu', $header);
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/imageeditor');
        /*
        $cnt = new SearchFilesController();
        $cnt->search();
        $this->set('searchController', $cnt);
        $result = $cnt->getSearchResultObject();
        if (is_object($result)) {
            $result = Loader::helper('json')->encode($result->getJSONObject());
            $v = View::getInstance();
			$token = \Core::make('token')->generate();
			$this->addFooterItem("<script type=\"text/javascript\">$(function() { $('div[data-search=files]').concreteFileManager({upload_token: '" . $token . "', result: " . $result . "}); });</script>");
        }*/
    }
}
