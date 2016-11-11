<?php
namespace Concrete\Core\Site;

use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Application\Application;

class Service
{

    protected $entityManager;
    protected $app;
    protected $config;
    protected $resolverFactory;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __construct(EntityManagerInterface $entityManager,
    Application $app, \Illuminate\Config\Repository $configRepository, ResolverFactory $resolverFactory)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->config = $configRepository;
        $this->resolverFactory = $resolverFactory;
    }

    public function getByType(Type $type)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->findByType($type);
    }

    public function getDefault()
    {
        $factory = new Factory($this->config);
        try {
            $site = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
                ->findOneBy(array('siteIsDefault' => true));
        } catch(\Exception $e) {
            return $factory->createDefaultEntity();
        }

        if (is_object($site)) {
            return $factory->createEntity($site);
        }
    }

    public function add(Type $type, Theme $theme, $handle, $name, $default = false)
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

        return $site;
    }

    public function getSiteTreeByID($siteTreeID)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Tree')
            ->find($siteTreeID);
    }

    public function getByID($id)
    {
        $site = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->find($id);
        $factory = new Factory($this->config);
        if (is_object($site)) {
            return $factory->createEntity($site);
        }
    }

    public function delete(Site $site)
    {

        $attributes = SiteKey::getAttributeValues($site);
        foreach($attributes as $av) {
            $this->entityManager->remove($av);
        }

        $this->entityManager->flush();

        $r = $this->entityManager->getConnection()
            ->executeQuery('select cID from Pages where siteTreeID = ? and cParentID = 0', [$site->getSiteTreeID()]);
        while ($row = $r->fetch()) {
            $page = \Page::getByID($row['cID']);
            if (is_object($page) && !$page->isError()) {
                $page->moveToTrash();
            }
        }
        $this->entityManager->remove($site);
        $this->entityManager->flush();
    }

    public function getList()
    {
        $sites = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->findAll();
        $list = array();
        $factory = new Factory($this->config);
        foreach($sites as $site) {
            $list[] = $factory->createEntity($site);
        }
        return $list;
    }

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
        $tree->setSiteHomePageID(HOME_CID);
        $tree->setLocale($locale);
        $locale->setSiteTree($tree);

        $site->getLocales()->add($locale);

        $service = $this->app->make('site/type');
        $type = $service->getDefault();
        $site->setType($type);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
    }

    final public function getSite()
    {
        return $this->resolverFactory->createResolver($this)->getSite();
    }

    final public function getActiveSiteForEditing()
    {
        return $this->resolverFactory->createResolver($this)->getActiveSiteForEditing();
    }

}
