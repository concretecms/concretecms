<?php
namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/user/search';

    protected function canAccess()
    {
        $tp = $this->app->make('helper/concrete/user');

        return $tp->canAccessUserSearchInterface();
    }

    public function view()
    {
        $this->set('multipleSelection', (bool)$this->request->query->get('multipleSelection', false));
    }
}
