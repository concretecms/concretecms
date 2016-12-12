<?php
namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Package;
use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
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
        return $this->getByID(1);
    }

    public function getByID($typeID)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findOneBy(array('siteTypeID' => $typeID));
    }

    public function getByHandle($typeHandle)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findOneBy(array('siteTypeHandle' => $typeHandle));
    }

    public function getByPackage(Package $package)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findByPackage($package);
    }

    public function getList()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findAll();
    }

    public function delete(Type $type)
    {
        $this->entityManager->remove($type);
        $this->entityManager->flush();
    }

    public function import($handle, $name, Package $pkg = null)
    {
        return $this->add($handle, $name, $pkg);
    }

    public function add($handle, $name, Package $pkg = null)
    {
        $factory = new Factory();
        $type = $factory->createEntity();
        $type->setSiteTypeHandle($handle);
        $type->setSiteTypeName($name);
        $type->setPackage($pkg);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }

    public function installDefault()
    {
        $factory = new Factory();
        $type = $factory->createDefaultEntity();

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }


}
