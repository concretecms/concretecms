<?php
namespace Concrete\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\SiteTypeCategory;
use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Entity\Attribute\Value\SiteTypeValue;
use Concrete\Core\Entity\Site\Domain;
use Concrete\Core\Entity\Site\Group\Relation;
use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Concrete\Core\Site\Type\Controller\Manager;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntrySiteResults;
use Concrete\Core\User\Group\Group;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Punic\Comparer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Site\Type\Service as SiteTypeService;

class Service
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var ResolverFactory
     */
    protected $resolverFactory;

    /**
     * @var \Concrete\Core\Cache\Cache
     */
    protected $cache;

    /**
     * @var SiteTypeService
     */
    protected $siteTypeService;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __construct(
        EntityManagerInterface $entityManager,
        Application $app,
        Repository $configRepository,
        ResolverFactory $resolverFactory,
        SiteTypeService $siteTypeService
    )
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->config = $configRepository;
        $this->cache = $this->app->make('cache/request');
        $this->resolverFactory = $resolverFactory;
        $this->siteTypeService = $siteTypeService;
    }

    /**
     * @param mixed $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Type $type
     *
     * @return Site[]
     */
    public function getByType(Type $type)
    {
        $sites = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->findByType($type);
        $return = [];
        foreach ($sites as $site) {
            $factory = new Factory($this->config);
            $return[] = $factory->createEntity($site);
        }

        return $return;
    }

    /**
     * @param string $handle
     *
     * @return Site|null
     */
    public function getByHandle($handle)
    {
        $item = $this->cache->getItem(sprintf('/site/handle/%s', $handle));
        if (!$item->isMiss()) {
            $site = $item->get();
        } else {
            $site = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
                ->findOneBy(['siteHandle' => $handle]);

            $factory = new Factory($this->config);
            if (is_object($site)) {
                $site = $factory->createEntity($site);
            }
            $this->cache->save($item->set($site));
        }

        return $site;
    }

    /**
     * @return Site|null
     */
    public function getDefault()
    {
        $item = $this->cache->getItem(sprintf('/site/default'));
        if (!$item->isMiss()) {
            $site = $item->get();
        } else {
            $factory = new Factory($this->config);
            try {
                $site = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
                    ->findOneBy(['siteIsDefault' => true]);
            } catch (\Exception $e) {
                return $factory->createDefaultEntity();
            }
            if (is_object($site)) {
                $site = $factory->createEntity($site);
            }
            $this->cache->save($item->set($site));
        }

        return $site;
    }

    /**
     * @param Type $type
     * @param Theme $theme
     * @param string $handle
     * @param string $name
     * @param string $locale
     * @param bool $default
     *
     * @return Site
     */
    public function add(Type $type, Theme $theme, $handle, $name, $locale, $default = false)
    {
        $factory = new Factory($this->config);
        $site = $factory->createEntity();
        $site->setSiteHandle($handle);
        $site->setIsDefault($default);
        $site->setType($type);
        $site->setThemeID($theme->getThemeID());
        $site->getConfigRepository()->save('name', $name);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        $data = explode('_', $locale);
        $locale = new Locale();
        $locale->setSite($site);
        $locale->setIsDefault(true);
        $locale->setLanguage($data[0]);
        $locale->setCountry($data[1]);

        $localeService = new \Concrete\Core\Localization\Locale\Service($this->entityManager);
        $localeService->updatePluralSettings($locale);

        $this->entityManager->persist($locale);
        $this->entityManager->flush();

        $this->entityManager->refresh($site);

        $skeletonService = $this->siteTypeService->getSkeletonService();
        $skeleton = $skeletonService->getSkeleton($type);
        if ($skeleton) {
            $skeletonService->publishSkeletonToSite($skeleton, $site);
        }

        // Add the default groups
        $groupService = $this->siteTypeService->getGroupService();
        $parent = $groupService->createSiteGroup($site);
        $groups = $groupService->getSiteTypeGroups($type);
        if ($groups) {
            foreach ($groups as $group) {
                /**
                 * @var $group \Concrete\Core\Entity\Site\Group\Group
                 */
                $siteGroup = $groupService->createInstanceGroup($group, $parent);
                $relation = new Relation();
                $relation->setSite($site);
                $relation->setInstanceGroupID($siteGroup->getGroupID());
                $relation->setSiteGroup($group);
                $group->getSiteGroupRelations()->add($relation);
                $this->entityManager->persist($group);
            }
        }

        // Add the default attributes
        if ($skeleton) {
            $attributes = $skeletonService->getAttributeValues($skeleton);
            foreach ($attributes as $attribute) {
                /**
                 * @var $attribute SiteTypeValue
                 */
                $site->setAttribute($attribute->getAttributeKey(), $attribute->getValueObject());
            }
        }

        $this->entityManager->flush();

        // Populate all the config values from the default site into this new site.
        // This is not ideal since it will fail if we don't update it after we add new
        // config values, but this will have to do for now

        // This code is not great, let's find a better solution
        /*
        $defaultSite = $this->getDefault();
        $defaultSiteConfig = $defaultSite->getConfigRepository();
        $config = $site->getConfigRepository();
        if ($defaultSiteConfig) {
            foreach (['user', 'editor'] as $key) {
                $config->save($key, $defaultSiteConfig->get($key));
            }
        }
        */

        /**
         * @var $manager Manager;
         */
        $request = Request::createFromGlobals();
        $controller = $this->getController($site);
        $site = $controller->add($site, $request);

        return $site;
    }

    public function getController(Site $site)
    {
        return $this->siteTypeService->getController($site->getType());
    }

    /**
     * @param int $siteTreeID
     *
     * @return \Concrete\Core\Entity\Site\Tree|null
     */
    public function getSiteTreeByID($siteTreeID)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Tree')
            ->find($siteTreeID);
    }

    /**
     * @param int $id
     *
     * @return Site|null
     */
    public function getByID($id)
    {
        $site = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->find($id);
        $factory = new Factory($this->config);
        if (is_object($site)) {
            return $factory->createEntity($site);
        }
    }

    /**
     * @param Site $site
     */
    public function delete(Site $site)
    {
        // Delete group relations
        $relations = $this->entityManager->getRepository(Relation::class)
            ->findBySite($site);
        foreach($relations as $relation) {
            $this->entityManager->remove($relation);
        }
        $this->entityManager->flush();

        // Run the site type controller delete method.
        $controller = $this->getController($site);
        $controller->delete($site);

        // Delete attribute values.
        $attributes = SiteKey::getAttributeValues($site);
        foreach ($attributes as $av) {
            $this->entityManager->remove($av);
        }

        $this->entityManager->flush();


        // Delete all pages in the site.
        $r = $this->entityManager->getConnection()
            ->executeQuery('select cID from Pages where siteTreeID = ? and cParentID = 0', [$site->getSiteTreeID()]);
        while ($row = $r->fetch()) {
            $page = Page::getByID($row['cID']);
            if (is_object($page) && !$page->isError()) {
                $page->moveToTrash();
            }
        }

        // Delete the locales
        $locales = $site->getLocales();
        $service = new \Concrete\Core\Localization\Locale\Service($this->entityManager);

        foreach ($locales as $locale) {
            $service->delete($locale);
        }

        // Finally, remove the site.
        $this->entityManager->remove($site);
        $this->entityManager->flush();
    }

    /**
     * Returns a list of sites. If $sort = 'name' then the sites will be sorted by site name. If sort is false it will
     * not be sorted. Only name is supported for now.
     *
     * @param string $sort
     *
     * @return Site[]
     */
    public function getList($sort = 'name')
    {
        $sites = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->findAll();
        $list = [];
        $factory = new Factory($this->config);
        foreach ($sites as $site) {
            $list[] = $factory->createEntity($site);
        }

        switch ($sort) {
            case 'name':
                $comparer = new Comparer();
                usort($list, function ($siteA, $siteB) use ($comparer) {
                    return $comparer->compare($siteA->getSiteName(), $siteB->getSiteName());
                });
                break;
        }

        return $list;
    }

    /**
     * @param string|null $locale
     *
     * @return Site
     */
    public function installDefault($locale = null)
    {
        if (!$locale) {
            $locale = Localization::BASE_LOCALE;
        }

        $siteConfig = $this->config->get('site');
        $defaultSite = array_get($siteConfig, 'default');

        $factory = new Factory($this->config);
        $site = $factory->createEntity();
        $site->setSiteHandle(array_get($siteConfig, "sites.{$defaultSite}.handle"));
        $site->setIsDefault(true);

        $data = explode('_', $locale);
        $locale = new Locale();
        $locale->setSite($site);
        $locale->setIsDefault(true);
        $locale->setLanguage($data[0]);
        $locale->setCountry($data[1]);

        $tree = new SiteTree();
        $cID = false;
        $connection = $this->entityManager->getConnection();
        if ($connection && $connection->tableExists('MultilingualSections')) {
            $cID = $connection->fetchColumn('select cID from MultilingualSections where msLanguage = ? and msCountry = ?', [$data[0], $data[1]]);
        }
        if (!$cID) {
            $cID = (int) Page::getHomePageID();
            if ($cID === 0) {
                $cID = HOME_CID;
            }
        }
        $tree->setSiteHomePageID($cID);
        $tree->setLocale($locale);
        $locale->setSiteTree($tree);

        $site->getLocales()->add($locale);

        $service = $this->siteTypeService;
        $type = $service->getDefault();
        $site->setType($type);

        $localeService = new \Concrete\Core\Localization\Locale\Service($this->entityManager);
        $localeService->updatePluralSettings($locale);

        $this->entityManager->persist($site);
        $this->entityManager->persist($tree);
        $this->entityManager->persist($locale);
        $this->entityManager->flush();

        $this->cache->delete('site');

        return $site;
    }

    /**
     * Resolve the active site
     * This method MUST be treated as `final` by extending drivers, but MAY be replaced by a complete override.
     *
     * @return Site|null
     */
    public function getSite()
    {
        $item = $this->cache->getItem('site');
        if (!$item->isMiss()) {
            $site = $item->get();
        } else {
            $site = $this->resolverFactory->createResolver($this)->getSite();
            $this->cache->save($item->set($site));
        }

        return $site;
    }


    /**
     * Resolve the active site for editing
     * This method MUST be treated as `final` by extending drivers, but MAY be replaced by a complete override.
     *
     * @return Site|null
     */
    public function getActiveSiteForEditing()
    {
        return $this->resolverFactory->createResolver($this)->getActiveSiteForEditing();
    }

    public function getSiteDomains(Site $site)
    {
        $domains = $this->entityManager->getRepository(Domain::class)
            ->findBySite($site);
        return $domains;
    }

    public function getSiteByDomain($domain)
    {
        $domain = $this->entityManager->getRepository(Domain::class)
            ->findOneByDomain($domain);
        if ($domain) {
            $factory = new Factory($this->config);
            return $factory->createEntity($domain->getSite());
        }
    }

    /**
     * Resolve the site instance associated with a given results node ID
     *
     * This is usually useful when resolving a site object from an express entry.
     * You'd do `$service->getByExpressNodeID($entry->getResultsNodeID());`
     *
     * @param int $resultsNodeID
     *
     * @return Site|null
     */
    public function getSiteByExpressResultsNodeID(int $resultsNodeID): ?Site
    {
        /** @TODO Use an overridable way to resolve results nodes */
        $siteResultsNode = Node::getByID($resultsNodeID);
        if ($siteResultsNode instanceof ExpressEntrySiteResults) {
            return $this->getSiteByExpressResultsNode($siteResultsNode);
        }

        return null;
    }

    /**
     * Resolve a site instance using an express results node
     *
     * This is typically useful when resolving the site from an express entry.
     *
     * @see Service::getSiteByExpressResultsNodeID()
     *
     * @param ExpressEntrySiteResults $siteResultsNode
     *
     * @return Site|null
     */
    public function getSiteByExpressResultsNode(ExpressEntrySiteResults $siteResultsNode): ?Site
    {
        return $this->getByID($siteResultsNode->getSiteID());
    }


}
