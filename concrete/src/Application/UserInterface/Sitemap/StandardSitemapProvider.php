<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\SiteGroup;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\LocaleEntry;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\SiteEntry;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\StandardTreeCollection;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\Service;
use Concrete\Core\Site\Tree\TreeInterface;

class StandardSitemapProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $permissionsIgnored = false;

    /**
     * @var \Concrete\Core\Cookie\CookieJar
     */
    protected $cookieJar;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Site\Service
     */
    protected $siteService;

    /**
     * StandardSitemapProvider constructor.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Cookie\CookieJar $cookies
     * @param \Concrete\Core\Site\Service $siteService
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Application $app, CookieJar $cookies, Service $siteService, Request $request)
    {
        $this->siteService = $siteService;
        $this->cookieJar = $cookies;
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Ignore the permissions.
     */
    public function ignorePermissions()
    {
        $this->permissionsIgnored = true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface::getTreeCollection()
     *
     * @return \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\StandardTreeCollection
     */
    public function getTreeCollection(Tree $selectedTree = null)
    {
        $collection = new StandardTreeCollection();
        $sites = $this->siteService->getList();

        // Do we use the locales as our entry, or do we use the site?
        if ($this->useLocales($sites)) {
            foreach ($sites as $site) {
                foreach ($site->getLocales() as $locale) {
                    if ($this->checkPermissions($locale)) {
                        $entry = new LocaleEntry($locale);
                        if ($selectedTree && $entry->getSiteTreeID() == $selectedTree->getSiteTreeID()) {
                            $entry->setIsSelected(true);
                        }
                        $collection->addEntry($entry);
                    }
                }
            }
        } else {
            foreach ($sites as $site) {
                if ($this->checkPermissions($site)) {
                    $entry = new SiteEntry($site);
                    if ($selectedTree && $entry->getSiteTreeID() == $selectedTree->getSiteTreeID()) {
                        $entry->setIsSelected(true);
                    }
                    $collection->addEntry($entry);
                }
            }
        }

        if ($this->useGroups($sites)) {
            foreach ($sites as $site) {
                $collection->addEntryGroup(new SiteGroup($site));
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface::includeMenuInResponse()
     */
    public function includeMenuInResponse()
    {
        if (($this->request->query->has('cParentID') && $this->request->query->get('cParentID'))
        || ($this->request->query->has('reloadNode') && $this->request->query->get('reloadNode'))) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface::getRequestedSiteTree()
     *
     * @return \Concrete\Core\Entity\Site\Tree|null
     */
    public function getRequestedSiteTree()
    {
        $query = $this->request->query;
        $cookieKey = 'ConcreteSitemapTreeID';
        if ($query->has('sitemapIndex') && $query->get('sitemapIndex') > 0) {
            $cookieKey .= '-' . (int) $query->get('sitemapIndex');
        }
        if ($query->has('siteTreeID') && $query->get('siteTreeID') > 0) {
            $this->cookieJar->getResponseCookies()->addCookie($cookieKey, $query->get('siteTreeID'));

            return $this->siteService->getSiteTreeByID($query->get('siteTreeID'));
        } else {
            // Check if the site id in $cookieKey is valid
            $site = null;
            if ($this->cookieJar->has($cookieKey)) {
                $site = $this->siteService->getSiteTreeByID($this->cookieJar->get($cookieKey));
            }
            if (is_object($site)) {
                return $site;
            }

            // the site id is not valid, let's get the default site
            $site = $this->siteService->getActiveSiteForEditing();
            $siteTreeID = null;

            $locale = $site->getDefaultLocale();
            if ($locale && $this->checkPermissions($locale)) {
                $siteTreeID = $locale->getSiteTreeObject()->getSiteTreeID();
            }

            if (!$siteTreeID) {
                // This means we don't have permission to view the default locale.
                // So instead we just grab the first we can find that we DO have permission
                // to view.
                foreach ($site->getLocales() as $locale) {
                    if ($this->checkPermissions($locale)) {
                        $siteTreeID = $locale->getSiteTreeObject()->getSiteTreeID();
                    }
                }
            }

            if ($siteTreeID) {
                // update $cookieKey to use a valid site id
                $this->cookieJar->getResponseCookies()->addCookie($cookieKey, $siteTreeID);
                return $this->siteService->getSiteTreeByID($siteTreeID);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface::getRequestedNodes()
     */
    public function getRequestedNodes()
    {
        $dh = $this->getSitemapDataProvider();
        if ($this->cookieJar->has('ConcreteSitemap-expand')) {
            $openNodeArray = explode(',', str_replace('_', '', $this->cookieJar->get('ConcreteSitemap-expand')));
            if (is_array($openNodeArray)) {
                $dh->setExpandedNodes($openNodeArray);
            }
        }
        if (!$this->includeMenuInResponse()) {
            if ($this->request->query->get('reloadSelfNode')) {
                $nodes = [$dh->getNode($this->request->query->get('cID'))];
            } else {
                $nodes = $dh->getSubNodes($this->request->query->get('cParentID'));
            }
        } else {
            $nodes = $dh->getSubNodes($this->getRequestedSiteTree());
        }

        return $nodes;
    }

    /**
     * @param \Concrete\Core\Entity\Site\Site[] $sites
     *
     * @return bool
     */
    protected function useGroups($sites)
    {
        if (count($sites) < 2) {
            return false;
        }

        return $this->useLocales($sites);
    }

    /**
     * @param \Concrete\Core\Site\Tree\TreeInterface $object
     *
     * @return bool
     */
    protected function checkPermissions(TreeInterface $object)
    {
        if (!$this->permissionsIgnored) {
            $home = $object->getSiteTreeObject()->getSiteHomePageObject();
            if ($home) {
                $cp = new Checker($home);

                return $cp->canViewPageInSitemap();
            }
        }

        return true;
    }

    /**
     * @param \Concrete\Core\Entity\Site\Site[] $sites
     *
     * @return bool
     */
    protected function useLocales($sites)
    {
        foreach ($sites as $site) {
            $locales = $site->getLocales();
            if (count($locales) > 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Concrete\Core\Application\Service\Dashboard\Sitemap
     */
    protected function getSitemapDataProvider()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        $queryString = $this->request->query;
        $dh->setDisplayNodePagination($queryString->get('displayNodePagination') ? true : false);
        $dh->setIsSitemapOverlay($queryString->get('isSitemapOverlay') ? true : false);
        $dh->setIncludeSystemPages($queryString->get('includeSystemPages') ? true : false);

        return $dh;
    }
}
