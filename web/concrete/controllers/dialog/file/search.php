<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Search\FileFolder;
use FilePermissions;
use Loader;
use Concrete\Controller\Search\Files as SearchFilesController;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/search';

    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles() || $cp->canAddFile()) {
            return true;
        } else {
            return false;
        }
    }

    public function view()
    {
        $provider = \Core::make('Concrete\Core\File\Search\SearchProvider');
        $query = $provider->getSessionCurrentQuery();
        if (is_object($query)) {
            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL(\URL::to('/ccm/system/search/files/current'));
            $result->setQuery($query);
        } else {
            $search = new FileFolder();
            $search->search();
            $result = $search->getSearchResultObject();
        }

        if (is_object($result)) {
            $this->set('result', $result);
        }

        $this->requireAsset('select2');
    }
}
