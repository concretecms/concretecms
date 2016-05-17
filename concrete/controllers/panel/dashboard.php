<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Controller\Element\Dashboard\Navigation;
use Concrete\Controller\Element\Navigation\Menu;
use Concrete\Core\Application\Service\DashboardMenu;
use Cookie;
use Loader;
use Page;
use BlockType;
use Symfony\Component\HttpFoundation\JsonResponse;
use User;
use UserInfo;

class Dashboard extends BackendInterfacePageController
{
    protected $viewPath = '/panels/dashboard';

    protected function canAccess()
    {
        $dh = Loader::helper('concrete/dashboard');

        return $dh->canRead();
    }

    protected function toggleFavorite($action)
    {
        $h = \Core::make('helper/concrete/dashboard');
        if ($h->inDashboard($this->page) && $this->permissions->canViewPage()) {
            \Core::make("helper/concrete/ui")->clearInterfaceItemsCache();
            $u = new User();
            if (\Core::make('token')->validate('access_bookmarks', $this->request->query->get('ccm_token'))) {
                $qn = DashboardMenu::getMine();
                if ($action == 'add' && !$qn->contains($this->page)) {
                    $qn->add($this->page);
                } else if ($qn->contains($this->page)) {
                    $qn->remove($this->page);
                }
                $u->saveConfig('QUICK_NAV_BOOKMARKS', serialize($qn));
                return new JsonResponse(['action' => $action]);
            }
        }
    }

    public function addFavorite()
    {
        return $this->toggleFavorite('add');
    }

    public function removeFavorite()
    {
        return $this->toggleFavorite('remove');
    }

    public function view()
    {
        if ($this->request->get('tab')) {
            Cookie::set('panels/dashboard/tab', $this->request->get('tab'));
            $tab = $this->request->get('tab');
        } else {
            $tab = Cookie::get('panels/dashboard/tab');
        }

        $nav = array();
        if ($tab != 'favorites') {
            $nav = new \Concrete\Controller\Element\Navigation\DashboardMenu($this->page);
            $this->set('nav', $nav);
        } else {
            $dh = Loader::helper('concrete/dashboard');
            $qn = \Concrete\Core\Application\Service\DashboardMenu::getMine();
            foreach ($qn->getItems() as $path) {
                $c = Page::getByPath($path, 'ACTIVE');
                if (is_object($c) && !$c->isError()) {
                    $nav[] = $c;
                }
            }
            $this->set('nav', $nav);
        }

        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $this->set('ui', $ui);
        $this->set('tab', $tab);
    }
}
