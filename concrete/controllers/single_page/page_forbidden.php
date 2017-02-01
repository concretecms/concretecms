<?php
namespace Concrete\Controller\SinglePage;

use PageController;
use User;
use Config;

class PageForbidden extends PageController
{
    protected $viewPath = '/frontend/page_forbidden';

    public function on_start()
    {
        $u = new User();
        if (!$u->isRegistered() && Config::get('concrete.permissions.forward_to_login')) { //if they are not logged in, and we show guests the login...
            $this->redirect('/login');
        }
    }
}
