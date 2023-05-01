<?php

namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManager;

class FullNavigationFactory
{

    /**
     * @var NavigationCache
     */
    protected $cache;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TreeMenuNavigationFactory
     */
    protected $treeMenuNavigationFactory;

    /**
     * @var DashboardSitemapNavigationFactory
     */
    protected $dashboardSitemapNavigationFactory;

    public function __construct(
        TreeMenuNavigationFactory $treeMenuNavigationFactory,
        DashboardSitemapNavigationFactory $dashboardSitemapNavigationFactory,
        Application $app,
        EntityManager $entityManager,
        NavigationCache $cache
    ) {
        $this->treeMenuNavigationFactory = $treeMenuNavigationFactory;
        $this->dashboardSitemapNavigationFactory = $dashboardSitemapNavigationFactory;
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function getMenu(): ?Menu
    {
        $dashboardMenuID = $this->app->make('config/database')->get('app.dashboard_menu');
        if ($dashboardMenuID) {
            return $this->entityManager->getRepository(Menu::class)->find($dashboardMenuID);
        }
        return null;
    }

    /**
     * Returns an entire dashboard navigation tree. Optionally starts at a particular section in the tree.
     * Used on the Dashboard home, intelligent search, mobile menu and more.
     *
     * @return Navigation
     */
    public function createNavigation(): Navigation
    {
        if (!$this->cache->has()) {
            $menu = $this->getMenu();
            if ($menu) {
                $navigation = $this->treeMenuNavigationFactory->createNavigation($menu->getTree());
            } else {
                $home = Page::getByPath('/dashboard');
                $navigation = $this->dashboardSitemapNavigationFactory->createNavigation($home);
            }
            $this->cache->set($navigation);
        } else {
            $navigation = $this->cache->get();
        }
        return $navigation;
    }

}
