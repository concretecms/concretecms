<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use FilePermissions;
use Loader;
use Concrete\Controller\Search\Files as SearchFilesController;

class AdvancedSearch extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/advanced_search';

    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        } else {
            return false;
        }
    }

    public function view()
    {

    }
}
