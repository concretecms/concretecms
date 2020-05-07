<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\Files\Header;
use Concrete\Controller\Search\FileFolder;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Field\ManagerFactory;
use FilePermissions;

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
        $search = $this->app->build(FileFolder::class);
        $search->search();
        $result = $search->getSearchResultObject();

        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
