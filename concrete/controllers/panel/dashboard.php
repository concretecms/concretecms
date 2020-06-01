<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Application\Service\DashboardMenu;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationCache;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationFactory;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationFactory;
use Concrete\Core\Navigation\Item\PageItem;
use Cookie;
use Loader;
use Page;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\User\User;
use UserInfo;
use URL;
use Core;
use PageList;

class Dashboard extends BackendInterfacePageController
{
    protected $viewPath = '/panels/dashboard';

    protected function canAccess()
    {
        $dh = Loader::helper('concrete/dashboard');

        return $dh->canRead();
    }

    public function view()
    {
        $menufactory = $this->app->make(NavigationFactory::class);
        $favoritesFactory = $this->app->make(FavoritesNavigationFactory::class);
        $menu = $menufactory->createNavigation();
        $favorites = $favoritesFactory->createNavigation();
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $this->set('menu', $menu);
        $this->set('favorites', $favorites);
        $this->set('ui', $ui);
    }

    protected function toggleFavorite($action)
    {
        $h = \Core::make('helper/concrete/dashboard');
        if ($h->inDashboard($this->page) && $this->permissions->canViewPage()) {
            $cache = $this->app->make(FavoritesNavigationCache::class);
            $cache->clear();
            $u = $this->app->make(User::class);
            if ($this->app->make('token')->validate('access_bookmarks', $this->request->query->get('ccm_token'))) {
                $navigation = $this->app->make(FavoritesNavigationFactory::class)->createNavigation();
                if ($action == 'add' && !$navigation->has(new PageItem($this->page))) {
                    $navigation->add(new PageItem($this->page));
                } elseif ($navigation->has(new PageItem($this->page))) {
                    $navigation->remove(new PageItem($this->page));
                }
                $u->saveConfig('DASHBOARD_FAVORITES', json_encode($navigation));

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

    /*

    public function view()
    {
        if ($this->request->get('tab')) {
            Cookie::set('panels/dashboard/tab', $this->request->get('tab'));
            $tab = $this->request->get('tab');
        } else {
            $tab = Cookie::get('panels/dashboard/tab');
        }

        $nav = [];
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

        $u = $this->app->make(User::class);
        $ui = UserInfo::getByID($u->getUserID());
        $this->set('ui', $ui);
        $this->set('tab', $tab);
    }

    //public function checkForMenu($c)
    //{
    //}
    public function checkForMenu($c)
    {
      //return (string) $this->renderActiveSubPanels($c) !== ''; // fails to pass triple equal sign checking on element where this function is called.
      return !($result = $this->renderActiveSubPanels($c)) || $result == '' ? null : 1;
    }

    public function renderActiveSubPanels($c)
    {
        $nh = Core::make('helper/navigation');
        $trail = $nh->getTrailToCollection($c);
        if (
            $c->getCollectionPath() == '/dashboard' ||
            $c->getCollectionPath() == '/dashboard/system'
           ) {
            $emulatePageID = $c->getCollectionID();
            $emulate = true;
        }
        if (
            $c->getCollectionPath() == '/account/edit_profile' ||
            $c->getCollectionPath() == '/account/messages'
           ) {
            $emulatePageID = $c->getCollectionParentID();
            $emulate = true;
        }
        if ($emulate === true) {
            $emulatePage = Page::getByID($emulatePageID);
            $trail = [$emulatePage, 0];
        }
        if (is_array($trail)) {
            array_pop($trail);
            $trail = array_reverse($trail);
            ob_start();
            for ($i = 0; $i < count($trail); ++$i) {
                $page = $trail[$i];
                $label = $page->getCollectionName();
                $cID = $page->getCollectionID();
                $parentID = $page->getCollectionParentID();
                $parent = Page::getByID($parentID);
                $parentTitle = $parent->getCollectionName();
                $parentParentID = $parent->getCollectionParentID();
                $parentParent = Page::getByID($parentParentID);
                $parentParentTitle = $parentParent->getCollectionName();
                $parentMenu = new PageList();
                $parentMenu->filterByExcludeNav(false);
                $parentMenu->sortByDisplayOrder();
                $parentMenu->filterByParentID($cID);
                $parentMenu->includeSystemPages();
                $parentMenu->includeAliases();
                $menuItems = $parentMenu->getResults();
                if ($i + 1 == count($trail)) {
                    $panelState = 'ccm-panel-content-visible';
                } else {
                    $panelState = 'ccm-panel-slide-left';
                }
                if ($c->getCollectionPath() == '/dashboard/welcome/me') {
                    break;
                }
                if (!$menuItems) {
                    break;
                }
                echo '<div class="ccm-panel-content ' . $panelState . '">';
                echo   '<section id="ccm-panel-dashboard-submenu-' . $parentID . '">';
                echo      '<header>';
                echo        '<a href="" data-panel-navigation="back" class="ccm-panel-back">';
                echo          '<svg><use xlink:href="#icon-arrow-left"></use></svg>';
                echo            '<span>' . $parentTitle . '</span>';
                echo        '</a>';
                echo        '<h4>' . $label . '</h4>';
                echo      '</header>';
                echo      '<div class="ccm-panel-content-inner">';
                echo        '$ui';
                foreach ($menuItems as $menuItem) {
                    $menuItemID = $menuItem->getCollectionID();
                    $class = '';
                    if ($menuItemID == $c->getCollectionID()) {
                        $class = 'active';
                    }
                    echo '<li class="' . $class . '">';
                    $parentMenu2 = new PageList();
                    $parentMenu2->filterByExcludeNav(false);
                    $parentMenu2->sortByDisplayOrder();
                    $parentMenu2->filterByParentID($menuItem->getCollectionID());
                    $parentMenu2->includeSystemPages();
                    $parentMenu2->includeAliases();
                    $menuItems2 = $parentMenu2->getResults();
                    if ($menuItem->getNumChildren() > 0 && $menuItems2) {
                        echo   '<a href="#" data-launch-sub-panel-url="' . URL::to('/ccm/system/panels/dashboard/load_menu') . '" data-load-menu="' . $menuItem->getCollectionID() . '">';
                        echo     $menuItem->getCollectionName();
                        echo   '</a>';
                    } else {
                        echo   '<a href="' . $menuItem->getCollectionLink() . '">';
                        echo   $menuItem->getCollectionName();
                        echo   '</a>';
                        echo '</li>';
                    }
                }
                echo        '</ul>';
                echo      '</div>';
                echo      '';
                echo   '</section>';
                echo '</div>';
            }
            $panels = ob_get_contents();
            //$panels .= $javascript;
            ob_end_clean();

            return $panels;
        } else {
            //return 'trail is not an array';
        }
    }*/
}
