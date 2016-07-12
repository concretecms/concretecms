<?php
namespace Concrete\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;
use Doctrine\ORM\EntityManagerInterface;

class Service
{

    protected $entityManager;
    protected $config;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __construct(EntityManagerInterface $entityManager, \Illuminate\Config\Repository $configRepository)
    {
        $this->entityManager = $entityManager;
        $this->config = $configRepository;
    }

    public function getDefault()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Site')
            ->findOneBy(array('siteIsDefault' => true));
    }

    public function add($handle, $name, $default = false)
    {
        $site = new Site($this->config);
        $site->setSiteHandle($handle);
        $site->setIsDefault($default);
        $site->getConfigRepository()->save('name', $name);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
    }

    public function installDefault()
    {
        $site = new Site($this->config);
        $site->setSiteHandle($this->config->get('concrete.sites.default.handle'));
        $site->setIsDefault(true);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $site;
    }

}
