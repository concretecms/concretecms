<?php
namespace Concrete\Core\Site;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Doctrine\ORM\EntityManagerInterface;

class Service
{

    protected $entityManager;
    protected $config;
    protected $resolverFactory;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __construct(EntityManagerInterface $entityManager, \Illuminate\Config\Repository $configRepository, ResolverFactory $resolverFactory)
    {
        $this->entityManager = $entityManager;
        $this->config = $configRepository;
        $this->resolverFactory = $resolverFactory;
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

    public function add($handle, $name, $default = false)
    {
        $factory = new Factory($this->config);
        $site = $factory->createEntity();
        $site->setSiteHandle($handle);
        $site->setIsDefault($default);
        $site->getConfigRepository()->save('name', $name);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
    }

    public function createHomePage(Site $site, Template $template)
    {
        $tree = new Tree();
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        $home = \Page::addHomePage($tree);
        $home->update(['cName' => $site->getSiteName(), 'pTemplateID' => $template->getPageTemplateID()]);

        $tree->setSiteHomePageID($home->getCollectionID());
        $site->setSiteTree($tree);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $home;
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

        $page = $site->getSiteHomePageObject();
        if (is_object($page)) {
            $page->moveToTrash();
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

    public function installDefault()
    {
        $siteConfig = $this->config->get('site');
        $defaultSite = array_get($siteConfig, 'default');

        $factory = new Factory($this->config);
        $site = $factory->createEntity();
        $site->setSiteHandle(array_get($siteConfig, "sites.{$defaultSite}.handle"));
        $site->setIsDefault(true);

        $tree = new Tree();
        $tree->setSiteHomePageID(HOME_CID);
        $site->setSiteTree($tree);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
    }

    final public function getSite()
    {
        return $this->resolverFactory->createResolver($this)->getSite();
    }


}
