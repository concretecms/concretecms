<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Controller\Element\Dashboard\Navigation;
use Concrete\Core\Application\Service\DashboardMenu;
use Cookie;
use Loader;
use Page;
use BlockType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\User\User;
use UserInfo;
use URL;
use Core;

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
            $u = $this->app->make(User::class);
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

        $u = $this->app->make(User::class);
        $ui = UserInfo::getByID($u->getUserID());
        $this->set('ui', $ui);
        $this->set('tab', $tab);
    }

    public function checkForMenu($c) {
      $result = $this->renderActiveSubPanels($c);
      if (!$result || $result == '') {
        return null;
      } else {
        return 1;
      }
    }

    public function renderActiveSubPanels($c) {
      $nh = Core::make('helper/navigation');
      $trail = $nh->getTrailToCollection($c);
      $systemSettings = 79;
      $dashboard = 2;
      $systemSettingsPageID = 80;
      $accountID = 192;
      $messages = 195;
      $waiting = 186;
      if ($c->getCollectionPath() == '/dashboard') $emulatPageID = $systemPageID;
      if ($c->getCollectionPath() == '/dashboard/system') $emulatePageID = $systemSettings;
      if ($c->getCollectionPath() == '/account/edit_profile') $emulatePageID = $accountID;
      if ($c->getCollectionPath() == '/account/messages') $emulatePageID = $accountID;
      if (
        $c->getCollectionPath() == '/dashboard/system' ||
        $c->getCollectionPath() == '/account/edit_profile' ||
        $c->getCollectionPath() == '/account/messages'
      ) {
        $emulatePage = Page::getByID($emulatePageID);
        $trail = array($emulatePage, 0);
        //$javascript = "<script type='text/javascript'>$('.ccm-panel-content-visible').scrollTop(0);</scrit>";
      } else {
        //$filterByParent = true;
      }
      if (is_array($trail)) {
        array_pop($trail);
        $trail = array_reverse($trail);
        ob_start();
        for ($i = 0; $i < count($trail); $i++) {
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
          if ($i+1 == count($trail)) {
            $panelState = 'ccm-panel-content-visible';
          } else {
            $panelState = 'ccm-panel-slide-left';
          }
          if ($c->getCollectionPath() == '/dashboard/welcome/me') break;
          if (!$menuItems) break;
          echo '<div class="ccm-panel-content '.$panelState.'">';
          echo   '<section id="ccm-panel-dashboard-submenu-'.$parentID.'">';
          echo      '<header>';
          echo        '<a href="" data-panel-navigation="back" class="ccm-panel-back">';
          echo          '<svg><use xlink:href="#icon-arrow-left"></use></svg>';
          echo            '<span>'.$parentTitle.'</span>';
          echo        '</a>';
          echo        '<h4>'.$label.'</h4>';
          echo      '</header>';
          echo      '<div class="ccm-panel-content-inner">';
          echo        '<ul class="nav flex-column">';
          foreach ($menuItems as $menuItem) {
            $menuItemID = $menuItem->getCollectionID();
            $class = "";
            if ($menuItemID == $c->getCollectionID()) $class = "active";
            echo '<li class="'.$class.'">';
            $parentMenu2 = new PageList();
            $parentMenu2->filterByExcludeNav(false);
            $parentMenu2->sortByDisplayOrder();
            $parentMenu2->filterByParentID($menuItem->getCollectionID());
            $parentMenu2->includeSystemPages();
            $parentMenu2->includeAliases();
            $menuItems2 = $parentMenu2->getResults();
            if ($menuItem->getNumChildren() > 0 && $menuItems2) {
              echo   '<a href="#" data-launch-sub-panel-url="'.URL::to('/ccm/system/panels/dashboard/load_menu').'" data-load-menu="'.$menuItem->getCollectionID().'">';
              echo     $menuItem->getCollectionName();
              echo   '</a>';
            } else {
              echo   '<a href="'.$menuItem->getCollectionLink().'">';
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
    }
}
