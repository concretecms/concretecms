<?php
namespace Concrete\Core\Page\Controller;

use Config;
use Loader;
use Page;
use \PageController as CorePageController;

class AccountPageController extends CorePageController
{

    public $helpers = array('html', 'form', 'text');

    public function on_start()
    {
        $u = new \User();
        if (!$u->isRegistered()) {
            $this->replace('/login');
        }
        $this->error = Loader::helper('validation/error');
        $this->set('valt', Loader::helper('validation/token'));
        $this->set('av', Loader::helper('concrete/avatar'));

        $this->set('profile', \UserInfo::getByID($u->getUserID()));
    }

    public function on_before_render()
    {
        $this->set('error', $this->error);
    }


}
