<?php
namespace Concrete\Core\Application\Service\Dashboard;

use Concrete\Core\Entity\Site\Tree;
use Config;
use PageList;
use TaskPermission;
use Cookie;
use stdClass;
use Permissions;
use Page;
use PermissionKey;

class Sitemap
{
    /**
     * @var bool
     */
    protected $expandedNodes = array();
    /**
     * @var bool
     */
    protected $displayNodePagination = false;
    /**
     * @var bool
     */
    protected $includeSystemPages;

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
        if (!isset($this->includeSystemPages)) {
            $this->includeSystemPages = Cookie::get('includeSystemPages');
        }

        return $this->includeSystemPages;
    }

    /**
     * @param bool $systemPages
     */
    public function setIncludeSystemPages($systemPages)
    {
        $this->includeSystemPages = $systemPages;
        Cookie::set('includeSystemPages', $systemPages);
    }

    /**
     * @param bool $paginate
     */
    public function setDisplayNodePagination($paginate)
    {
        $this->displayNodePagination = $paginate;
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
            $cp = new \Permissions($page);

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
        } else if ($parent instanceof Tree) {
            $pl->setSiteTreeObject($parent);
            $cID = 0;
        }
        $pl->filterByParentID($cID); // Either 0 or cParentID
        $pl->setPageVersionToRetrieve(\Concrete\Core\Page\PageList::PAGE_VERSION_RECENT);

        if ($cID == 1) {
            $results = $pl->getResults();
            $pagination = null;
        } else {
            $pl->setItemsPerPage(Config::get('concrete.limits.sitemap_pages'));
            $pagination = $pl->getPagination();
            $total = $pagination->getTotalResults();
            $results = $pagination->getCurrentPageResults();
        }

        $nodes = array();
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
                $n->addClass = 'ccm-sitemap-explore';
                $n->noLink = true;
                $n->unselectable = true;
                $html = $pagination->renderView('dashboard');
                $n->title = $html;
                $nodes[] = $n;
            } else {
                $n = new stdClass();
                $n->icon = false;
                $n->addClass = 'ccm-sitemap-explore';
                $n->noLink = true;
                $n->active = false;
                $n->focus = false;
                $n->unselectable = true;
                $n->title = ' ' . t('More Pages to Display. <strong>Next Page &gt;</strong>');
                $n->href = (string) \URL::to('/dashboard/sitemap/explore/', $cID);
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

        $numSubpages = ($c->getNumChildren()  > 0) ? $c->getNumChildren()  : '';

        $cvName = ($c->getCollectionName() !== '') ? $c->getCollectionName() : '(No Title)';
        $cvName = ($c->isSystemPage() || $cID == 1) ? t($cvName) : $cvName;

        $isInTrash = $c->isInTrash();

        $isTrash = $c->getCollectionPath() == Config::get('concrete.paths.trash');
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
                if ($cID == 1) {
                    $cIconClass = 'fa fa-home';
                } elseif ($numSubpages > 0) {
                    $cIconClass = 'fa fa-folder-o';
                } else {
                    $cIconClass = 'fa fa-file-o';
                }
            }
        }

        $cAlias = $c->isAlias();
        $cPointerID = $c->getCollectionPointerID();
        if ($cAlias) {
            if ($cPointerID > 0) {
                $cIconClass = 'fa fa-sign-in';
                $cAlias = 'POINTER';
                $cID = $c->getCollectionPointerOriginalID();
            } else {
                $cIconClass = 'fa fa-sign-out';
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
        if ($cID == HOME_CID) {
            $node->addClass = 'ccm-page-home';
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

        if ($includeChildren && ($cID == 1 || $nodeOpen)) {
            // We open another level
            $node->children = $this->getSubNodes($cID, $onGetNode);
        }

        if ($onGetNode instanceof \Closure) {
            $node = $onGetNode($node);
        }

        return $node;
    }

    /**
     * @return bool
     */
    public function canRead()
    {
        $tp = new TaskPermission();

        return $tp->canAccessSitemap();
    }
}
