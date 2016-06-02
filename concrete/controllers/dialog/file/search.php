<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\Files\Header;
use Concrete\Controller\Search\FileFolder;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Field\ManagerFactory;
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
        $fields = $this->request->get('field');
        if (count($fields)) { // We are passing in something like "filter by images"
            $manager = ManagerFactory::get('file');
            $fields = $manager->getFieldsFromRequest($this->request->query->all());
            $query = new Query();
            $query->setFields($fields);
            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL((string) \URL::to('/ccm/system/search/files/basic'));
        } else {
            $search = new FileFolder();
            $search->search();
            $result = $search->getSearchResultObject();
        }

        if (is_object($result)) {
            $this->set('result', $result);
        }

        $this->set('header', new Header($query));
        $this->requireAsset('selectize');
    }
}
