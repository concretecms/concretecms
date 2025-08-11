<?php
namespace Concrete\Controller\Element\Dashboard\Api\Integrations;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\User\UserInfo;

class Search extends ElementController
{

    public function getElement()
    {
        return 'dashboard/api/integrations/search';
    }

    public function view()
    {
        $this->set('headerSearchAction', $this->app->make('url')->to('/dashboard/system/api/integrations'));
        $this->set('form', $this->app->make('helper/form'));
    }

}
