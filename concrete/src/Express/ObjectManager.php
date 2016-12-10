<?php
namespace Concrete\Core\Express;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class ObjectManager
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $entity = new Entity();
        $entity->setHandle($handle);
        $entity->setPluralHandle($plural_handle);
        $entity->setName($name);
        if ($pkg) {
            $entity->setPackage($pkg);
        }
        return $entity;
    }

    public function addObject($handle, $plural_handle, $name, Package $pkg = null)
    {
        $entity = $this->buildObject($handle, $name, $plural_handle, $pkg);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    public function getEntry($entryID)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneBy(['exEntryID' => $entryID]);
    }



}
