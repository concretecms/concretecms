<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Navigation\AccountMenu;
use Concrete\Controller\Element\Navigation\Menu;
use Concrete\Core\Page\Desktop\DesktopList;
use Loader;
use PageController as CorePageController;

class AccountPageController extends CorePageController
{
    public $helpers = array('html', 'form', 'text');

    public function on_start()
    {
        $u = new \User();
        if (!$u->isRegistered()) {
            return $this->replace('/login');
        }

        $dh = \Core::make('helper/concrete/dashboard');
        $desktop = DesktopList::getMyDesktop();
        if ($dh->inDashboard($desktop) && $this->getPageObject()->getCollectionPath() != '/account/welcome') {
            $this->theme = 'dashboard';
            $this->set('pageTitle', t('My Account'));
        }

        $this->setThemeViewTemplate('account.php');
        $this->error = Loader::helper('validation/error');
        $this->token = Loader::helper('validation/token');
        $this->set('valt', $this->token);
        $this->set('av', Loader::helper('concrete/avatar'));

        $this->set('profile', \UserInfo::getByID($u->getUserID()));

        $nav = new AccountMenu($this->getPageObject());
        $this->set('nav', $nav);

    }

    public function on_before_render()
    {
        $this->set('error', $this->error);
    }
}
