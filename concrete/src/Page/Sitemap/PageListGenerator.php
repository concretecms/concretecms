<?php

namespace Concrete\Core\Page\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\User\Group\Group;
use DateTime;

/**
 * Class to be used to generate the list of the pages that should be included in a sitemap.xml file.
 */
class PageListGenerator
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Entity\Site\Site|null|false
     */
    private $site = false;

    /**
     * @var \Concrete\Core\Multilingual\Page\Section\Section[]|null
     */
    private $multilingualSections = null;

    /**
     * @var bool|null
     */
    private $isMultilingualEnabled = null;

    /**
     * @var \Concrete\Core\Database\Connection\Connection|null
     */
    private $connection = null;

    /**
     * @var \Concrete\Core\Application\Service\Dashboard|null
     */
    private $dashboardHelper = null;

    /**
     * @var \DateTime|null
     */
    private $now = null;

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\PageKey|null|false
     */
    private $excludeFromSiteMapAttributeKey = false;

    /**
     * @var \Concrete\Core\Permission\Key\PageKey|null|false
     */
    private $viewPagePermissionKey = false;

    /**
     * @var \Concrete\Core\User\Group\Group|null|false
     */
    private $visitorsUserGroup = false;

    /**
     * @var \Concrete\Core\Permission\Access\Entity\GroupEntity|null|false
     */
    private $visitorsUserGroupAccessEntity = false;

    /**
     * Initialize the instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the approximage numnber of pages that will be included in the sitemap (the actual value can be lower than it).
     *
     * @return int
     */
    public function getApproximatePageCount()
    {
        $siteTreeIDList = array_merge([0], $this->getSiteTreesIDList());
        $connection = $this->getConnection();
        $result = $connection->fetchColumn('select count(*) from Pages where siteTreeID is null or siteTreeID in (' . implode(', ', $siteTreeIDList) . ')');

        return $result ?: 0;
    }

    /**
     * Generate the list of pages that should be included in the sitemap.
     *
     * @return \Concrete\Core\Page\Page|\Generator
     */
    public function generatePageList()
    {
        $this->now = new DateTime();
        $siteTreeIDList = array_merge([0], $this->getSiteTreesIDList());
        $connection = $this->getConnection();
        $rs = $connection->executeQuery('select cID from Pages where siteTreeID is null or siteTreeID in (' . implode(', ', $siteTreeIDList) . ')');
        while (($cID = $rs->fetchColumn()) !== false) {
            $page = Page::getByID($cID, 'ACTIVE');
            if ($page && $this->canIncludePageInSitemap($page)) {
                yield $page;
            }
        }
    }

    /**
     * Check if the current site has more than one multilingual section.
     *
     * @return bool
     */
    public function isMultilingualEnabled()
    {
        if ($this->isMultilingualEnabled === null) {
            $this->isMultilingualEnabled = count($this->getMultilingualSections()) > 1;
        }

        return $this->isMultilingualEnabled;
    }

    /**
     * Get the multilingual section where a page resides (if any).
     *
     * @param \Concrete\Core\Page\Page $page
     *
     * @return \Concrete\Core\Multilingual\Page\Section\Section|null
     */
    public function getMultilingualSectionForPage(Page $page)
    {
        $result = null;
        $siteTree = $page->getSiteTreeObject();
        if ($siteTree !== null) {
            $homeID = $siteTree->getSiteHomePageID();
            if ($homeID) {
                $mlSections = $this->getMultilingualSections();
                if (isset($mlSections[$homeID])) {
                    $result = $mlSections[$homeID];
                }
            }
        }

        return $result;
    }

    /**
     * Get the list of multilingual sections defined for the current site.
     *
     * @return \Concrete\Core\Multilingual\Page\Section\Section[]
     */
    public function getMultilingualSections()
    {
        if ($this->multilingualSections === null) {
            $site = $this->getSite();
            if ($site === null) {
                $this->multilingualSections = [];
            } else {
                $list = [];
                foreach (MultilingualSection::getList($site) as $section) {
                    $list[$section->getCollectionID()] = $section;
                }
                $this->multilingualSections = $list;
            }
        }

        return $this->multilingualSections;
    }

    /**
     * Should a page be included in the sitemap?
     *
     * @param Page $page
     *
     * @return bool
     */
    public function canIncludePageInSitemap(Page $page)
    {
        $result = false;
        if ($this->isPageStandard($page)) {
            if ($this->isPagePublished($page)) {
                if (!$this->isPageExcludedFromSitemap($page)) {
                    if ($this->isPageAccessible($page)) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get the currently used site.
     *
     * @return \Concrete\Core\Entity\Site\Site|null
     */
    public function getSite()
    {
        if ($this->site === false) {
            $this->site = $this->app->make('site')->getDefault();
        }

        return $this->site;
    }

    /**
     * Set the currently used site.
     *
     * @param Site $site
     *
     * @return $this
     */
    public function setSite(Site $site)
    {
        if ($this->site !== $site) {
            $this->site = $site;
            $this->multilingualSections = null;
            $this->isMultilingualEnabled = null;
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    protected function getNow()
    {
        if ($this->now === null) {
            $this->now = new DateTime();
        }

        return $this->now;
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey|null
     */
    protected function getExcludeFromSiteMapAttributeKey()
    {
        if ($this->excludeFromSiteMapAttributeKey === false) {
            $category = $this->app->make(PageCategory::class);
            $this->excludeFromSiteMapAttributeKey = $category->getAttributeKeyByHandle('exclude_sitemapxml');
        }

        return $this->excludeFromSiteMapAttributeKey;
    }

    /**
     * @return \Concrete\Core\Permission\Key\PageKey|null
     */
    protected function getViewPagePermissionKey()
    {
        if ($this->viewPagePermissionKey === false) {
            $this->viewPagePermissionKey = PermissionKey::getByHandle('view_page');
        }

        return $this->viewPagePermissionKey;
    }

    /**
     * @return \Concrete\Core\User\Group\Group|null
     */
    protected function getVisitorsUserGroup()
    {
        if ($this->visitorsUserGroup === false) {
            $this->visitorsUserGroup = Group::getByID(GUEST_GROUP_ID);
        }

        return $this->visitorsUserGroup;
    }

    /**
     * @return \Concrete\Core\Permission\Access\Entity\GroupEntity|null
     */
    protected function getVisitorsUserGroupAccessEntity()
    {
        if ($this->visitorsUserGroupAccessEntity === false) {
            $visitorsUserGroup = $this->getVisitorsUserGroup();
            $ae = $visitorsUserGroup === null ? null : GroupPermissionAccessEntity::getOrCreate($visitorsUserGroup);
            $this->visitorsUserGroupAccessEntity = $ae ?: null;
        }

        return $this->visitorsUserGroupAccessEntity;
    }

    /**
     * @return \Concrete\Core\Database\Connection\Connection
     */
    protected function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->app->make(Connection::class);
        }

        return $this->connection;
    }

    /**
     * @return \Concrete\Core\Application\Service\Dashboard
     */
    protected function getDashboardHelper()
    {
        if ($this->dashboardHelper === null) {
            $this->dashboardHelper = $this->app->make('helper/concrete/dashboard');
        }

        return $this->dashboardHelper;
    }

    /**
     * @return int[]
     */
    protected function getSiteTreesIDList()
    {
        $result = [];
        $site = $this->getSite();
        if ($site !== null) {
            foreach ($site->getLocales() as $siteLocale) {
                $siteTreeID = $siteLocale->getSiteTreeID();
                if ($siteTreeID) {
                    $result[] = $siteTreeID;
                }
            }
        }

        return $result;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    protected function isPageStandard(Page $page)
    {
        $result = false;
        if (!$page->isError() && !$page->isSystemPage() && !$page->isExternalLink() && !$page->isMasterCollection() && !$page->isInTrash()) {
            if (!$this->getDashboardHelper()->inDashboard($page)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    protected function isPagePublished(Page $page)
    {
        $result = false;
        $pubDate = $page->getCollectionDatePublic();
        if ($pubDate !== null && (new DateTime($pubDate)) <= $this->getNow()) {
            $pageVersion = $page->getVersionObject();
            if ($pageVersion && $pageVersion->isApproved()) {
                $startDate = $pageVersion->getPublishDate();
                if (!$startDate || new DateTIme($startDate) >= $this->getNow()) {
                    $endDate = $pageVersion->getPublishEndDate();
                    if (!$endDate || new DateTIme($endDate) <= $this->getNow()) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    protected function isPageExcludedFromSitemap(Page $page)
    {
        $ak = $this->getExcludeFromSiteMapAttributeKey();
        if ($ak === null) {
            $result = false;
        } else {
            $result = (bool) $page->getAttribute($ak);
        }

        return $result;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    protected function isPageAccessible(Page $page)
    {
        $viewPermission = $this->getViewPagePermissionKey();
        if ($viewPermission === null) {
            $result = false;
        } else {
            $viewPermission->setPermissionObject($page);
            $viewPermissionObject = $viewPermission->getPermissionAccessObject();
            if ($viewPermissionObject === null) {
                $result = false;
            } else {
                $ae = $this->getVisitorsUserGroupAccessEntity();
                if ($ae === null) {
                    $result = false;
                } else {
                    $result = (bool) $viewPermissionObject->validateAccessEntities([$ae]);
                }
            }
        }

        return $result;
    }
}
