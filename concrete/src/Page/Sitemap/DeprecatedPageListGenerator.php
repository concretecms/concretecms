<?php

namespace Concrete\Core\Page\Sitemap;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Page\Page;

/**
 * @deprecated Don't use! Create your own PageListGenerator class instead
 */
class DeprecatedPageListGenerator extends PageListGenerator
{
    /**
     * @var callable|null
     */
    private $deprecatedChecker = null;

    /**
     * @var array|null
     */
    private $instances = null;

    /**
     * @param callable $deprecatedChecker
     */
    public function setDeprecatedChecker(callable $deprecatedChecker)
    {
        $this->deprecatedChecker = $deprecatedChecker;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\PageListGenerator::canIncludePageInSitemap()
     */
    public function canIncludePageInSitemap(Page $page)
    {
        $dc = $this->deprecatedChecker;

        return $dc($page, $this->getInstances());
    }

    /**
     * @return array
     */
    private function getInstances()
    {
        if ($this->instances === null) {
            $pageAttributeCaetegory = $this->app->make(PageCategory::class);
            $this->instances = [
                'navigation' => $this->app->make('helper/navigation'),
                'dashboard' => $this->getDashboardHelper(),
                'view_page' => $this->getViewPagePermissionKey(),
                'guestGroup' => $this->getVisitorsUserGroup(),
                'now' => $this->getNow(),
                'ak_exclude_sitemapxml' => $this->getExcludeFromSiteMapAttributeKey(),
                'ak_sitemap_changefreq' => $pageAttributeCaetegory->getAttributeKeyByHandle('sitemap_changefreq'),
                'ak_sitemap_priority' => $pageAttributeCaetegory->getAttributeKeyByHandle('sitemap_priority'),
                'guestGroupAE' => $this->getVisitorsUserGroupAccessEntity(),
                'multilingualSections' => $this->isMultilingualEnabled() ? $this->getMultilingualSections() : [],
            ];
        }

        return $this->instances;
    }
}
