<?php
namespace Concrete\Core\Application\Service;

use Config;
use Core;
use Database;
use File;
use Localization;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use Session;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Support\Facade\Application;
use View;

class Dashboard
{
    /**
     * @var bool|null
     */
    protected $canRead;

    /**
     * @var bool|null
     */
    protected $canAccessComposer;

    /**
     * Checks to see if a user has access to the C5 dashboard.
     *
     * @return bool
     */
    public function canRead()
    {
        if ($this->canRead === null) {
            $c = Page::getByPath('/dashboard', 'ACTIVE');
            if ($c && !$c->isError()) {
                $cp = new Permissions($c);

                $this->canRead = $cp->canViewPage();
            }
        }

        return $this->canRead;
    }

    /**
     * @return bool
     */
    public function canAccessComposer()
    {
        if ($this->canAccessComposer === null) {
            $c = Page::getByPath('/dashboard/composer', 'ACTIVE');
            $cp = new Permissions($c);

            $this->canAccessComposer = $cp->canViewPage();
        }

        return $this->canAccessComposer;
    }

    /**
     * Test if the a page or path path is within the dashboard.
     * If an empty (or no) argument is passed, we'll check the current page.
     *
     * @param  \Concrete\Core\Page\Page|string|null $pageOrPath
     *
     * @return bool
     */
    public function inDashboard($pageOrPath = null)
    {
        $path = '';
        if (is_string($pageOrPath)) {
            $path = $pageOrPath;
        } elseif ($pageOrPath instanceof Page && !$pageOrPath->isError()) {
            $path = $pageOrPath->getCollectionPath();
        } else {
            $view = View::getRequestInstance();
            if ($view->getThemeHandle() == 'dashboard') {
                return true;
            }
            $page = Page::getCurrentPage();
            if ($page instanceof Page && !$page->isError()) {
                $path = $page->getCollectionPath();
            }
        }

        return $path === '/dashboard' || strpos($path, '/dashboard/') === 0;
    }

    /**
     * @deprecated
     */
    public function getDashboardPaneFooterWrapper($includeDefaultBody = true)
    {
        return;
    }

    /**
     * @deprecated
     */
    public function getDashboardPaneHeaderWrapper($title = false, $help = false, $span = 'span12', $includeDefaultBody = true, $navigatePages = [], $upToPage = false, $favorites = true)
    {
        return;
    }

}

class DashboardMenu
{
    /**
     * @var \Concrete\Core\Page\Page[]
     */
    protected $items;

    /**
     * @param bool $sort
     *
     * @return array
     */
    public function getItems($sort = true)
    {
        if ($sort) {
            usort($this->items, ['\Concrete\Core\Application\Service\DashboardMenu', 'sortItems']);
        }

        return $this->items;
    }

    /**
     * @param string $a
     * @param string $b
     *
     * @return int
     */
    protected static function sortItems($a, $b)
    {
        $subpatha = substr($a, 11); // /dashboard
        $subpathb = substr($b, 11); // /dashboard
        $segmentsa = explode('/', $subpatha);
        $segmentsb = explode('/', $subpathb);
        $segmenta = substr($subpatha, 0, strpos($subpatha, '/'));
        $segmentb = substr($subpathb, 0, strpos($subpathb, '/'));
        if (count($segmentsa) == 3 && count($segmentsb) == 3) {
            $subpatha = $segmenta[0] . '/' . $segmenta[1];
            $subpathb = $segmentb[0] . '/' . $segmentb[1];
            $segmenta .= '/' . $segmentsa[1];
            $segmentb .= '/' . $segmentsb[1];
        }

        if (!$segmenta) {
            $segmenta = $subpatha;
        }
        if (!$segmentb) {
            $segmentb = $subpathb;
        }
        $db = Database::connection();
        $displayorderA = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', ['/dashboard/' . $segmenta]));
        $displayorderB = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', ['/dashboard/' . $segmentb]));

        if ($displayorderA > $displayorderB) {
            return 1;
        } elseif ($displayorderA < $displayorderB) {
            return -1;
        } else {
            $displayorderA = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', ['/dashboard/' . $subpatha]));
            $displayorderB = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', ['/dashboard/' . $subpathb]));
            if ($displayorderA > $displayorderB) {
                return 1;
            } elseif ($displayorderA < $displayorderB) {
                return -1;
            }
        }
    }

    /**
     * @param \Concrete\Core\Page\Page $c
     *
     * @return bool
     */
    public function contains($c)
    {
        return in_array($c->getCollectionPath(), $this->items);
    }

    /**
     * @param \Concrete\Core\Page\Page $c
     */
    public function add($c)
    {
        $this->items[] = $c->getCollectionPath();
    }

    /**
     * @param \Concrete\Core\Page\Page $c
     */
    public function remove($c)
    {
        unset($this->items[array_search($c->getCollectionPath(), $this->items)]);
    }

    /**
     * @return DashboardMenu
     */
    public static function getMine()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(ConcreteUser::class);
        $qn = unserialize($u->config('QUICK_NAV_BOOKMARKS'));
        if (is_object($qn)) {
            return $qn;
        }
        $qn = new self();
        $qnx = new DefaultDashboardMenu();
        $qn->items = $qnx->items;

        return $qn;
    }
}

class DefaultDashboardMenu extends DashboardMenu
{
    /**
     * @var array
     */
    public $items = [
        '/dashboard/welcome',
        '/dashboard/composer/write',
        '/dashboard/composer/drafts',
        '/dashboard/sitemap/full',
        '/dashboard/sitemap/search',
        '/dashboard/files/search',
        '/dashboard/files/sets',
        '/dashboard/reports/forms',
    ];
}
