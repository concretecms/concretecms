<?php

namespace Concrete\Core\Navigation\Breadcrumb;

use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class PageBreadcrumbFactory
{
    /**
     * @var Navigation
     */
    protected $navigation;

    /**
     * @var bool
     */
    protected $includeCurrent = true;

    /**
     * @var bool
     */
    protected $ignoreExcludeNav = true;

    /**
     * @var bool
     */
    protected $ignorePermission = false;

    public function __construct(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * @param bool $includeCurrent
     */
    public function setIncludeCurrent(bool $includeCurrent): void
    {
        $this->includeCurrent = $includeCurrent;
    }

    /**
     * @param bool $ignoreExcludeNav
     */
    public function setIgnoreExcludeNav(bool $ignoreExcludeNav): void
    {
        $this->ignoreExcludeNav = $ignoreExcludeNav;
    }

    /**
     * @param bool $ignorePermission
     */
    public function setIgnorePermission(bool $ignorePermission): void
    {
        $this->ignorePermission = $ignorePermission;
    }

    public function shouldExcludeFromNav(Page $page): bool
    {
        if ($this->ignoreExcludeNav) {
            return false;
        }

        return (bool) $page->getAttribute('exclude_nav');
    }

    public function shouldExcludeSubpagesFromNav(Page $page): bool
    {
        if ($this->ignoreExcludeNav) {
            return false;
        }

        return (bool) $page->getAttribute('exclude_subpages_from_nav');
    }

    public function shouldExcludeCurrentPageFromNav(Page $page): bool
    {
        if ($this->includeCurrent) {
            return $this->shouldExcludeFromNav($page);
        }

        return true;
    }

    public function canViewPage(Page $page): bool
    {
        if ($this->ignorePermission) {
            return true;
        }

        $checker = new Checker($page);

        return $checker->canViewPage();
    }

    public function getBreadcrumb(Page $page): BreadcrumbInterface
    {
        $pages = array_reverse($this->navigation->getTrailToCollection($page));
        $breadcrumb = new PageBreadcrumb();
        /** @var Page $_page */
        foreach ($pages as $_page) {
            if (!$this->shouldExcludeFromNav($_page) && $this->canViewPage($_page)) {
                $breadcrumb->add(new Item($_page->getCollectionLink(), (string) $_page->getCollectionName()));
            }
            if ($this->shouldExcludeSubpagesFromNav($_page)) {
                $this->setIncludeCurrent(false);
                break;
            }
        }
        if (!$this->shouldExcludeCurrentPageFromNav($page) && $this->canViewPage($page)) {
            $breadcrumb->add(new Item($page->getCollectionLink(), $page->getCollectionName(), true));
        }

        return $breadcrumb;
    }
}
