<?php
namespace Concrete\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Resolver\Resolver;
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
        $site = $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->findOneBy(array('siteIsDefault' => true));
        if (is_object($site)) {
            $factory = new Factory($this->config);
            return $factory->createEntity($site);
        }
    }

    public function add($handle, $name, Page $page, $default = false)
    {
        $factory = new Factory($this->config);
        $site = $factory->createEntity();
        $site->setSiteHandle($handle);
        $site->setIsDefault($default);
        $site->setSiteHomePageID($page->getCollectionID());
        $site->getConfigRepository()->save('name', $name);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
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
        $page->moveToTrash();

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
        $factory = new Factory($this->config);
        $site = $factory->createEntity();
        $site->setSiteHandle($this->config->get('concrete.sites.default.handle'));
        $site->setIsDefault(true);
        $site->setSiteHomePageID(HOME_CID);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
    }

    final public function getSite()
    {
        return $this->resolverFactory->createResolver()->getSite();
    }


}
