<?php
namespace Concrete\Core\Application\Service\Dashboard;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Closure;
use stdClass;

class Sitemap
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var bool
     */
    protected $expandedNodes = [];

    /**
     * @var bool
     */
    protected $displayNodePagination = false;

    /**
     * @var bool
     */
    protected $isSitemapOverlay = false;

    /**
     * @var bool|null
     */
    protected $includeSystemPages = false;

    /**
     * @var bool|null
     */
    protected $canViewSitemapPanel;

    /**
     * @var bool|null
     */
    protected $canRead;

    /**
     * Sitemap constructor.
     *
     * @param \Concrete\Core\Application\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param bool $autoOpen
     */
    public function setExpandedNodes($nodes)
    {
        $this->expandedNodes = $nodes;
    }

    /**
     * @return bool
     */
    public function includeSystemPages()
    {
        return $this->includeSystemPages;
    }

    /**
     * @param bool $systemPages
     */
    public function setIncludeSystemPages($systemPages)
    {
        $this->includeSystemPages = (bool) $systemPages;
    }

    /**
     * @param bool $paginate
     */
    public function setDisplayNodePagination($paginate)
    {
        $this->displayNodePagination = $paginate;
    }

    /**
     * @param bool $isSitemapOverlay
     */
    public function setIsSitemapOverlay($isSitemapOverlay)
    {
        $this->isSitemapOverlay = $isSitemapOverlay;
    }

    /**
     * @param int $cID
     *
     * @return array
     */
    public function getSubNodes($parent, $onGetNode = null)
    {
        $pl = new PageList();
        $pl->setPermissionsChecker(function ($page) {
            $cp = new Permissions($page);

            return $cp->canViewPageInSitemap();
        });
        $pl->includeAliases();
        $pl->sortByDisplayOrder();
        if ($this->includeSystemPages()) {
            $pl->includeSystemPages();
            $pl->includeInactivePages();
        }
        if (!is_object($parent)) {
            $cID = $parent;
        } elseif ($parent instanceof Tree) {
            $pl->setSiteTreeObject($parent);
            $cID = 0;
        }
        $pl->filterByParentID($cID); // Either 0 or cParentID
        $pl->setPageVersionToRetrieve(\Concrete\Core\Page\PageList::PAGE_VERSION_RECENT);

        if ($cID == 1) {
            $results = $pl->getResults();
            $pagination = null;
        } else {
            $config = $this->app->make('config');
            $pl->setItemsPerPage($config->get('concrete.limits.sitemap_pages'));
            $pagination = $pl->getPagination();
            $total = $pagination->getTotalResults();
            $results = $pagination->getCurrentPageResults();
        }

        $nodes = [];
        foreach ($results as $c) {
            $n = $this->getNode($c, true, $onGetNode);
            if ($n != false) {
                $nodes[] = $n;
            }
        }
        if (is_object($pagination) && $pagination->haveToPaginate()) {
            if ($this->displayNodePagination && isset($pagination)) {
                $n = new stdClass();
                $n->icon = false;
                $n->extraClasses = 'ccm-sitemap-explore';
                $n->noLink = true;
                $n->unselectable = true;
                $html = $pagination->renderView('dashboard');
                $n->title = $html;
                $nodes[] = $n;
            } else {
                $n = new stdClass();
                $n->icon = false;
                $n->extraClasses = 'ccm-sitemap-explore';
                $n->noLink = true;
                $n->active = false;
                $n->focus = false;
                $n->unselectable = true;

                // Avoids redirecting to the flat view page in an overlay context
                if ($this->isSitemapOverlay) {
                    $n->extraClasses = 'ccm-sitemap-open-flat-view';
                    $n->cParentID = (int) $cID;
                    $n->title = ' ' . t('More Pages to Display. <strong>Use the Flat View</strong>');
                } else {
                    $n->title = ' ' . t('More Pages to Display. <strong>Next Page &gt;</strong>');
                    $n->href = (string) \URL::to('/dashboard/sitemap/explore/', $cID);
                }
                $nodes[] = $n;
            }
        }

        return $nodes;
    }

    /**
     * @param \Concrete\Core\Page\Page|int $cItem
     * @param bool $includeChildren
     *
     * @return stdClass
     */
    public function getNode($cItem, $includeChildren = true, $onGetNode = null)
    {
        if (!is_object($cItem)) {
            $cID = $cItem;
            $c = Page::getByID($cID, 'RECENT');
        } else {
            $cID = $cItem->getCollectionID();
            $c = $cItem;
        }

        $cp = new Permissions($c);
        $canEditPageProperties = $cp->canEditPageProperties();
        $canEditPageSpeedSettings = $cp->canEditPageSpeedSettings();
        $canEditPagePermissions = $cp->canEditPagePermissions();
        $canEditPageDesign = ($cp->canEditPageTheme() || $cp->canEditPageTemplate());
        $canEditPageType = $cp->canEditPageType();
        $canViewPageVersions = $cp->canViewPageVersions();
        $canDeletePage = $cp->canDeletePage();
        $canAddSubpages = $cp->canAddSubpage();
        $canAddExternalLinks = $cp->canAddExternalLink();

        $nodeOpen = false;
        if (in_array($cID, $this->expandedNodes)) {
            $nodeOpen = true;
        }

        if ($c->getCollectionPointerID()) {
            $numSubpages = 0;
        } else {
            $numSubpages = (int) $c->getNumChildren();
        }

        $cvName = ($c->getCollectionName() !== '') ? $c->getCollectionName() : '(No Title)';
        $cvName = ($c->isSystemPage() || $cID == 1) ? t($cvName) : $cvName;

        $isInTrash = $c->isInTrash();

        $config = $this->app->make('config');
        $isTrash = $c->getCollectionPath() == $config->get('concrete.paths.trash');
        if ($isTrash || $isInTrash) {
            $pk = PermissionKey::getByHandle('empty_trash');
            if (!$pk->validate()) {
                return false;
            }
        }

        if ($c->getAttribute('icon_dashboard')) {
            $cIconClass = $c->getAttribute('icon_dashboard'); // use markup with custom class name rather than image
        } else {
            $cIconClass = null;
            $cIcon = $c->getCollectionIcon();
            if (!$cIcon) {
                if ($c->isHomePage()) {
                    $cIconClass = 'icon-home';
                } elseif ($numSubpages > 0) {
                    $cIconClass = 'icon-folder';
                } else {
                    $cIconClass = 'icon-page';
                }
            }
        }

        $cAlias = $c->isAlias();
        $cPointerID = $c->getCollectionPointerID();
        if ($cAlias) {
            if ($cPointerID > 0) {
                $cIconClass = 'fas fa-sign-out-alt';
                $cAlias = 'POINTER';
                $cID = $c->getCollectionPointerOriginalID();
            } else {
                $cIconClass = 'fas fa-external-link-alt';
                $cAlias = 'LINK';
            }
        }

        /*
        $node = array(
            'cIcon' => $cIcon,
            'cAlias' => $cAlias,
            'numSubpages'=> $numSubpages,
        );

        */

        $node = new stdClass();
        $node->title = $cvName;
        $node->link = $c->getCollectionLink();
        if ($numSubpages > 0) {
            $node->lazy = true;
        }
        if ($cIconClass) {
            $node->icon = $cIconClass;
        } else {
            $node->icon = $cIcon;
        }
        if ($c->isHomePage()) {
            $node->extraClasses = 'ccm-page-home';
            $node->expanded = true;
        }
        if ($nodeOpen) {
            $node->expanded = true;
        }
        $node->cAlias = $cAlias;
        $node->isInTrash = $isInTrash;
        $node->numSubpages = $numSubpages;
        $node->isTrash = $isTrash;
        $node->cID = $cID;
        $node->key = $cID;
        $node->ptID = $c->getPageTypeID();
        $node->canEditPageProperties = $canEditPageProperties;
        $node->canEditPageSpeedSettings = $canEditPageSpeedSettings;
        $node->canEditPagePermissions = $canEditPagePermissions;
        $node->canEditPageDesign = $canEditPageDesign;
        $node->canEditPageType = $canEditPageType;
        $node->canViewPageVersions = $canViewPageVersions;
        $node->canDeletePage = $canDeletePage;
        $node->canAddSubpages = $canAddSubpages;
        $node->canAddExternalLinks = $canAddExternalLinks;

        if ($includeChildren && ($c->isHomePage() || $nodeOpen)) {
            // We open another level
            $node->children = $this->getSubNodes($cID, $onGetNode);
        }

        if ($onGetNode instanceof Closure) {
            $node = $onGetNode($node);
        }

        return $node;
    }

    public function canViewSitemapPanel()
    {
        if ($this->canViewSitemapPanel === null) {
            $types = Type::getList();
            foreach ($types as $pt) {
                $ptp = new Permissions($pt);
                if ($ptp->canAddPageType()) {
                    $this->canViewSitemapPanel = true;
                    break;
                }
            }
            if ($this->canViewSitemapPanel === null) {
                $this->canViewSitemapPanel = $this->canRead();
            }
        }

        return $this->canViewSitemapPanel;
    }

    /**
     * @return bool
     */
    public function canRead()
    {
        if ($this->canRead === null) {
            $tp = new Permissions();
            $this->canRead = $tp->canAccessSitemap();
        }

        return $this->canRead;
    }
}
