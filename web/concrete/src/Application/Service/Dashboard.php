<?php
namespace Concrete\Core\Application\Service;

use Config;
use Core;
use Database;
use File;
use Page;
use URL;
use Localization;
use User as ConcreteUser;
use Permissions;
use Session;

class Dashboard
{
    /**
     * Checks to see if a user has access to the C5 dashboard.
     *
     * @return bool
     */
    public function canRead()
    {
        $c = Page::getByPath('/dashboard', 'ACTIVE');
        $cp = new Permissions($c);

        return $cp->canViewPage();
    }

    /**
     * @return bool
     */
    public function canAccessComposer()
    {
        $c = Page::getByPath('/dashboard/composer', 'ACTIVE');
        $cp = new Permissions($c);

        return $cp->canViewPage();
    }

    /**
     * Test if the current path is within the dashboard.
     * Optionally, a Page or path can be passed to test.
     *
     * @param  bool|\Concrete\Core\Page\Page|string $page (optional)
     *
     * @return bool
     */
    public function inDashboard($page = false)
    {
        $path = "";
        if ($page instanceof Page && !$page->isError()) {
            $path = $page->getCollectionPath();
        } elseif (is_string($page)) {
            $path = $page;
        } else {
            $page = Page::getCurrentPage();
            if ($page instanceof Page && !$page->isError()) {
                $path = $page->getCollectionPath();
            }
        }

        return strpos($path, '/dashboard') === 0;
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
    public function getDashboardPaneHeaderWrapper($title = false, $help = false, $span = 'span12', $includeDefaultBody = true, $navigatePages = array(), $upToPage = false, $favorites = true)
    {
        return;
    }

    /**
     * @param bool $title
     * @param bool $help
     * @param array $navigatePages
     * @param bool $upToPage
     * @param bool $favorites
     *
     * @return string
     */
    public function getDashboardPaneHeader($title = false, $help = false, $navigatePages = array(), $upToPage = false, $favorites = true)
    {
        $c = Page::getCurrentPage();
        $vt = Core::make('helper/validation/token');
        $token = $vt->generate('access_quick_nav');

        $nh = Core::make('helper/navigation');
        $trail = $nh->getTrailToCollection($c);
        if (count($trail) > 1 || count($navigatePages) > 1 || is_object($upToPage)) {
            $parent = Page::getByID($c->getCollectionParentID());
            if (count($trail) > 1 && (!is_object($upToPage))) {
                $upToPage = Page::getByID($parent->getCollectionParentID());
            }
            $subpages = array();
            if ($navigatePages !== -1) {
                if (count($navigatePages) > 0) {
                    $subpages = $navigatePages;
                } else {
                    /*
                     * @var \Concrete\Core\Page\Page[]
                     */
                    $subpages = \Concrete\Block\Autonav\Controller::getChildPages($parent);
                }
            }

            $subpagesP = array();
            if (is_array($subpages)) {
                foreach ($subpages as $sc) {
                    $cp = new Permissions($sc);
                    if ($cp->canViewPage()) {
                        $subpagesP[] = $sc;
                    }
                }
            }

            if (count($subpagesP) > 0 || is_object($upToPage)) {
                $relatedPages = '<ul id="ccm-page-navigate-pages-content" class="dropdown-menu">';

                foreach ($subpagesP as $sc) {
                    if ($sc->getAttribute('exclude_nav')) {
                        continue;
                    }

                    if ($c->getCollectionPath() == $sc->getCollectionPath() || (strpos($c->getCollectionPath(), $sc->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $sc->getCollectionPath()) !== false) {
                        $class = 'nav-selected';
                    } else {
                        $class = '';
                    }

                    $relatedPages .= '<li class="' . $class . '"><a href="' . $nh->getLinkToCollection($sc) . '">' . t($sc->getCollectionName()) . '</a></li>';
                }

                if ($upToPage) {
                    $relatedPages .= '<li class="ccm-menu-separator"></li>';
                    $relatedPages .= '<li><a href="' . $nh->getLinkToCollection($upToPage) . '">' . t('&lt; Back to %s', t($upToPage->getCollectionName())) . '</a></li>';
                }
                $relatedPages .= '</ul>';
                $navigateTitle = t($parent->getCollectionName());
            }
        }

        $html = '<div class="ccm-pane-header">';

        /*$class = 'icon-star';
        $qn = ConcreteDashboardMenu::getMine();
        $quicknav = $qn->getItems(false);
        if (in_array($c->getCollectionPath(), $quicknav)) {
            $class = 'icon-white icon-star';
        }
        */
        $html .= '<ul class="ccm-pane-header-icons">';
        if (!$help) {
            $ih = Core::make('helper/concrete/ui/help');
            $pageHelp = $ih->getPages();
            if (isset($pageHelp[$c->getCollectionPath()])) {
                $help = $pageHelp[$c->getCollectionPath()];
            }
        }

        if (is_array($help)) {
            $help = $help[0] . '<br/><br/><a href="' . $help[1] . '" class="btn small" target="_blank">' . t('Learn More') . '</a>';
        }

        if (isset($relatedPages)) {
            $html .= '<li><a href="" data-toggle="dropdown" title="' . $navigateTitle . '" id="ccm-page-navigate-pages"><i class="icon-share-alt"></i></a>' . $relatedPages . '</li>';
        }

        if ($help) {
            $html .= '<li><span style="display: none" id="ccm-page-help-content">' . $help . '</span><a href="javascript:void(0)" title="' . t('Help') . '" id="ccm-page-help"><i class="icon-question-sign"></i></a></li>';
        }

        if ($favorites) {
            $html .= '<li><a href="javascript:void(0)" id="ccm-add-to-quick-nav" onclick="CCMDashboard.toggleQuickNav(' . $c->getCollectionID() . ',\'' . $token . '\')"><i class="' . $class . '"></i></a></li>';
        }

        $html .= '<li><a href="javascript:void(0)" onclick="CCMDashboard.closePane(this)"><i class="icon-remove"></i></a></li>';
        $html .= '</ul>';
        if (!$title) {
            $title = $c->getCollectionName();
        }
        $html .= '<h3>' . $title . '</h3>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return \stdClass
     */
    public function getDashboardBackgroundImage()
    {
        // this feed is an array of standard PHP objects with a SRC, a caption, and a URL
        // allow for a custom white-label feed
        $filename = date('Ymd') . '.jpg';
        $obj = new \stdClass();
        $obj->checkData = false;
        $obj->displayCaption = false;
        $image = '';

        if (Config::get('concrete.white_label.dashboard_background')) {
            $image = Config::get('concrete.white_label.dashboard_background');
            if ($image == 'none') {
                $image = '';
            }
        } else {
            $obj->checkData = true;
            $imageSetting = Config::get('concrete.misc.dashboard_background_image');
            if ($imageSetting == 'custom') {
                $fo = File::getByID(Config::get('concrete.misc.dashboard_background_image_fid'));
                if (is_object($fo)) {
                    $image = $fo->getRelativePath();
                }
            } elseif ($imageSetting == 'none') {
                $image = '';
            } else {
                if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
                    $image = Config::get('concrete.urls.background_feed_secure') . '/' . $filename;
                } else {
                    $image = Config::get('concrete.urls.background_feed') . '/' . $filename;
                }
                $obj->displayCaption = true;
            }
        }
        $obj->filename = $filename;
        $obj->image = $image;

        return $obj;
    }

    /**
     * @return mixed
     */
    public function getIntelligentSearchMenu()
    {
        $dashboardMenus = Session::get('dashboardMenus', array());
        $dashboardMenusKey = Localization::activeLocale();
        if (array_key_exists($dashboardMenusKey, $dashboardMenus)) {
            return $dashboardMenus[$dashboardMenusKey];
        }

        ob_start();
        ?>
            <div id="ccm-intelligent-search-results">
            <?php
            $page = Page::getByPath('/dashboard');
        $children = $page->getCollectionChildrenArray(true);
        $navHelper = Core::make('helper/navigation');

        $packagepages = array();
        $corepages = array();
        foreach ($children as $ch) {
            $page = Page::getByID($ch);
            $pageP = new Permissions($page);
            if ($pageP->canRead()) {
                if (!$page->getAttribute("exclude_nav")) {
                    if ($page->getPackageID() > 0) {
                        $packagepages[] = $page;
                    } else {
                        $corepages[] = $page;
                    }
                }
            } else {
                continue;
            }

            if ($page->getAttribute('exclude_search_index')) {
                continue;
            }

            if ($page->getCollectionPath() == '/dashboard/system') {
                $ch2 = $page->getCollectionChildrenArray();
            } else {
                $ch2 = $page->getCollectionChildrenArray(true);
            }
            ?>

                <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">

                <h1><?=t($page->getCollectionName())?></h1>


                <ul class="ccm-intelligent-search-results-list">
                <?php
                if (count($ch2) == 0) {
                    ?>
                    <li><a href="<?=$navHelper->getLinkTocollection($page)?>"><?=t($page->getCollectionName())?></a><span><?=t($page->getCollectionName())?> <?=t($page->getAttribute('meta_keywords'))?></span></li>
                    <?php

                }
            ?>

                <?php
                if ($page->getCollectionPath() == '/dashboard/system') {
                    ?>
                    <li><a href="<?=$navHelper->getLinkTocollection($page)?>"><?=t('View All')?></a><span><?=t($page->getCollectionName())?> <?=t($page->getAttribute('meta_keywords'))?></span></li>
                    <?php

                }

            foreach ($ch2 as $chi) {
                $subpage = Page::getByID($chi);
                $subpageP = new Permissions($subpage);
                if (!$subpageP->canRead()) {
                    continue;
                }

                if ($subpage->getAttribute('exclude_search_index')) {
                    continue;
                }

                ?>
                    <li><a href="<?=$navHelper->getLinkTocollection($subpage)?>"><?=t($subpage->getCollectionName())?></a><span><?php if ($page->getCollectionPath() != '/dashboard/system') {
    ?><?=t($page->getCollectionName())?> <?=t($page->getAttribute('meta_keywords'))?> <?php 
}
                ?><?=t($subpage->getCollectionName())?> <?=t($subpage->getAttribute('meta_keywords'))?></span></li>
                    <?php

            }
            ?>
                </ul>

                </div>
                <?php

        }

        $custHome = Page::getByPath('/dashboard/home');
        $custHomeP = new Permissions($custHome);
        if ($custHomeP->canRead()) {
            ?>

            <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">

            <h1><?=t('Dashboard Home')?></h1>


            <ul class="ccm-intelligent-search-results-list">
                <li><a href="<?=URL::to('/dashboard/home')?>"><?=t('Customize')?> <span><?=t('Customize Dashboard Home')?></span></a></li>
            </ul>

            </div>

            <?php

        }
        ?>

            <div class="ccm-intelligent-search-results-module">
            <h1><?=t('Your Site')?></h1>
            <div class="loader">
                <div class="dot dot1"></div>
                <div class="dot dot2"></div>
                <div class="dot dot3"></div>
                <div class="dot dot4"></div>
            </div>
            <ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-your-site">
            </ul>
            </div>

            <?php if (Config::get('concrete.external.intelligent_search_help')) {
    ?>
            <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite">
            <h1><?=t('Help')?></h1>
            <div class="loader">
                <div class="dot dot1"></div>
                <div class="dot dot2"></div>
                <div class="dot dot3"></div>
                <div class="dot dot4"></div>
            </div>
            <ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-help">
            </ul>
            </div>
            <?php

}
        ?>

            <?php if (Config::get('concrete.marketplace.intelligent_search')) {
    ?>
            <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite">
            <h1><?=t('Add-Ons')?></h1>
            <div class="loader">
                <div class="dot dot1"></div>
                <div class="dot dot2"></div>
                <div class="dot dot3"></div>
                <div class="dot dot4"></div>
            </div>
            <ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-marketplace">
            </ul>
            </div>
            <?php

}
        ?>
        </div>

        <?php
        $html = ob_get_contents();
        ob_end_clean();
        $dashboardMenus[$dashboardMenusKey] = str_replace(array("\n", "\r", "\t"), "", $html);
        Session::set('dashboardMenus', $dashboardMenus);

        return $dashboardMenus[$dashboardMenusKey];
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
            usort($this->items, array('\Concrete\Core\Application\Service\DashboardMenu', 'sortItems'));
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
        $displayorderA = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $segmenta)));
        $displayorderB = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $segmentb)));

        if ($displayorderA > $displayorderB) {
            return 1;
        } elseif ($displayorderA < $displayorderB) {
            return -1;
        } else {
            $displayorderA = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $subpatha)));
            $displayorderB = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $subpathb)));
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
        $u = new ConcreteUser();
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
    public $items = array(
        '/dashboard/composer/write',
        '/dashboard/composer/drafts',
        '/dashboard/sitemap/full',
        '/dashboard/sitemap/search',
        '/dashboard/files/search',
        '/dashboard/files/sets',
        '/dashboard/reports/statistics',
        '/dashboard/reports/forms',
    );
}
