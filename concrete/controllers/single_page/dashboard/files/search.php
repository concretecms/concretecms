<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Controller\Element\Search\Files\Header;
use Concrete\Controller\Search\FileFolder;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\Result\Result;
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
        $this->set('breadcrumbClass', 'ccm-file-manager-breadcrumb');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/imageeditor');

        $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
        $query = $provider->getSessionCurrentQuery();
        if (is_object($query)) {
            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL(\URL::to('/ccm/system/search/files/current'));
        } else {
            $search = new FileFolder();
            $search->search();
            $result = $search->getSearchResultObject();
            
        }

        if (is_object($result)) {
            $this->set('result', $result);
            $result = json_encode($result->getJSONObject());
            $token = \Core::make('token')->generate();
            $this->addFooterItem(
                "<script type=\"text/javascript\">$(function() { $('#ccm-dashboard-content').concreteFileManager({upload_token: '" . $token . "', result: " . $result . "}); });</script>"
            );
        }
    }
}
