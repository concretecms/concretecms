<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Express\Entry\Manager as EntryManager;
use Concrete\Core\Express\Controller\Manager as ControllerManager;

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

    public function refresh($object)
    {
        $this->entityManager->refresh($object);
        return $object;
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

    public function buildEntry($entity)
    {
        $entity = is_string($entity) ? $this->getObjectByHandle($entity) : $entity;
        if ($entity instanceof ObjectBuilder) {
            $entity = $entity->getEntity();
        }
        $builder = $this->app->make(EntryBuilder::class);
        $builder->createEntry($entity);
        return $builder;
    }

    public function getEntry($entryID)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneBy(['exEntryID' => $entryID]);
    }

    public function deleteEntry($entryID)
    {
        $manager = $this->app->make(EntryManager::class);
        $entry = $this->getEntry($entryID);
        if (is_object($entry)) {
            $manager->deleteEntry($entry);
        }
    }

    public function getObjectByID($entityID)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($entityID);
    }

    public function getObjectByHandle($entityHandle)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByHandle($entityHandle);
    }

    public function getEntityController(Entity $entity)
    {
        return $this->app->make(ControllerManager::class)->driver(
            $entity->getHandle()
        );
    }

}
