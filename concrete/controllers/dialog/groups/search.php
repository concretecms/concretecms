<?php
namespace Concrete\Controller\Dialog\Groups;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use TaskPermission;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/groups/search';

    protected function canAccess()
    {
        $tp = new TaskPermission();

        return $tp->canAccessGroupSearch();
    }

    public function view()
    {
    }
}