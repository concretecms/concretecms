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
        // Moved all the logic for this into the view because we have to reference the view in multiple spots
        // and passing the data around is tedious.
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

}
