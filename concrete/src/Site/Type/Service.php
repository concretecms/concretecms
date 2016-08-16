<?php
namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Doctrine\ORM\EntityManagerInterface;

class Service
{

    protected $entityManager;
    protected $app;

    public function __construct(Application $application, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->app = $application;
    }

    public function getDefault()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findOneBy(array('siteTypeID' => 1));
    }

    public function add($handle, $name)
    {
        $factory = new Factory();
        $type = $factory->createEntity();
        $type->setSiteTypeHandle($handle);
        $type->setSiteTypeName($name);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }

    public function installDefault()
    {
        $factory = new Factory();
        $type = $factory->createDefaultEntity();

        /**
         * @var $site Site
         */
        $site = $this->app->make('site')->getDefault();
        $site->setType($type);

        $this->entityManager->persist($site);
        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }


}
