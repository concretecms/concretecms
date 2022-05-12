<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Package;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Express\Entry\Manager as EntryManager;
use Concrete\Core\Express\Controller\Manager as ControllerManager;
use Symfony\Component\HttpFoundation\Request;

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
            return $r->findBy(['include_in_public_list' => true, 'is_published' => true]);
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

    /**
     * Entry ID may be the integer ID or the public identifier
     * @param int|string $entryID
     * @return object
     */
    public function getEntry($entryID)
    {
        $numberValidator = $this->app->make(Numbers::class);
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        if ($numberValidator->integer($entryID)) {
            return $r->findOneBy(['exEntryID' => $entryID]);
        } else {
            return $this->getEntryByPublicIdentifier($entryID);
        }
    }

    /**
     * @param $publicIdentifier
     * @return object
     */
    public function getEntryByPublicIdentifier($publicIdentifier)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        return $r->findOneBy(['publicIdentifier' => $publicIdentifier]);
    }
    
    /**
     * @param int|Entry $entry
     */
    public function deleteEntry($entry)
    {
        if (!$entry instanceof Entry) {
            $entry = $this->getEntry($entry);
        }
        if ($entry) {
            /**
             * @var $entry Entry
             */
            $entity = $entry->getEntity();
            if ($entity) {
                $request = Request::createFromGlobals();
                $controller = $this->getEntityController($entity);
                $manager = $controller->getEntryManager($request);
                $manager->deleteEntry($entry);
            }
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
