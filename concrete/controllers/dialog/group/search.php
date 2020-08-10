<?php
namespace Concrete\Controller\Dialog\Group;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use TaskPermission;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/group/search';

    protected function canAccess()
    {
        $tp = new TaskPermission();

        return $tp->canAccessGroupSearch();
    }

    public function view()
    {
    }
}
