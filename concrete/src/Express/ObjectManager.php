<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class ObjectManager
{

    protected $entityManager;
    protected $app;

    public function __construct(Application $app, EntityManagerInterface $entityManager)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    public function getEntities($asObject = false)
    {
        $r = $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity');
        if ($asObject) {
            return $r;
        } else {
            return $r->findBy(['include_in_public_list' => true]);
        }
    }

    public function getList($entityHandle, $asObject = false)
    {
        $entity = $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByHandle($entityHandle);
        $list = new EntryList($entity);
        if ($asObject) {
            return $list;
        } else {
            return $list->getResults();
        }
    }

    public function buildObject($handle, $plural_handle, $name, Package $pkg = null)
    {
        $builder = $this->app->make(ObjectBuilder::class);
        $builder->createObject($name);
        $builder->setHandle($handle);
        $builder->setPluralHandle($plural_handle);
        if ($pkg) {
            $builder->setPackage($pkg);
        }
        return $builder;
    }

    public function getEntry($entryID)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneBy(['exEntryID' => $entryID]);
    }

    public function getObjectByHandle($entityHandle)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByHandle($entityHandle);
    }


}
