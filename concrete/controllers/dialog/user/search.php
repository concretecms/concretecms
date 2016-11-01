<?php
namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\Users\Header;
use Loader;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/user/search';

    protected function canAccess()
    {
        $tp = Loader::helper('concrete/user');

        return $tp->canAccessUserSearchInterface();
    }

    public function view()
    {
        $search = $this->app->make('Concrete\Controller\Search\Users');
        $result = $search->getCurrentSearchObject();

        if (is_object($result)) {
            $this->set('result', $result);
        }

        $header = new Header();
        $this->set('header', $header);
    }
}
