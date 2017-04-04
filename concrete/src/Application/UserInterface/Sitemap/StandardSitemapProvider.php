<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\Provider\GroupProvider;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\SiteGroup;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\LocaleEntry;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Provider\SiteLocaleProvider;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\SiteEntry;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\StandardTreeCollection;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollection;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Site\Service;

class StandardSitemapProvider implements ProviderInterface
{

    /**
     * @var $siteService Service
     */
    protected $siteService;

    /**
     * StandardSitemapProvider constructor.
     * @param Service $siteService
     */
    public function __construct(Service $siteService)
    {
        $this->siteService = $siteService;
    }

    protected function useGroups($sites)
    {
        if (count($sites) < 2) {
            return false;
        }

        return $this->useLocales($sites);
    }

    protected function useLocales($sites)
    {
        foreach($sites as $site) {
            $locales = $site->getLocales();
            if (count($locales) > 1) {
                return true;
            }
        }
        return false;
    }


    public function getTreeCollection(SiteTree $selectedTree = null)
    {
        $collection = new StandardTreeCollection();
        $sites = $this->siteService->getList();

        // Do we use the locales as our entry, or do we use the site?
        if ($this->useLocales($sites)) {
            foreach($sites as $site) {
                foreach($site->getLocales() as $locale) {
                    $entry = new LocaleEntry($locale);
                    if ($selectedTree && $entry->getSiteTreeID() == $selectedTree->getSiteTreeID()){
                        $entry->setIsSelected(true);
                    }
                    $collection->addEntry($entry);
                }
            }
        } else {
            foreach($sites as $site) {
                $entry = new SiteEntry($site);
                if ($selectedTree && $entry->getSiteTreeID() == $selectedTree->getSiteTreeID()){
                    $entry->setIsSelected(true);
                }
                $collection->addEntry($entry);
            }
        }

        if ($this->useGroups($sites)) {
            foreach($sites as $site) {
                $collection->addEntryGroup(new SiteGroup($site));
            }
        }

        return $collection;
    }
}
