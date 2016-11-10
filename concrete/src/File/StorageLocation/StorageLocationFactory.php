<?php

namespace Concrete\Core\File\StorageLocation;

use Concrete\Core\Database\DatabaseManagerORM;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StorageLocationFactory
 * Get ahold of existing storage locations and create new ones
 * @package Concrete\Core\File\StorageLocation
 */
class StorageLocationFactory
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(DatabaseManagerORM $manager)
    {
        $this->entityManager = $manager->entityManager();
    }

    /**
     * Create a new StorageLocation, pass this to StorageLocationFactory->persist() to store this location
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @param $name
     * @return StorageLocation
     */
    public function create(ConfigurationInterface $configuration, $name)
    {
        $location = new StorageLocation();
        $location->setConfigurationObject($configuration);
        $location->setName($name);

        return $location;
    }

    /**
     * Store a created storage location to the database
     * @param StorageLocation $storageLocation
     * @return StorageLocation The persisted location, may not be the same as the passed in object
     */
    public function persist(StorageLocation $storageLocation)
    {
        return $this->entityManager->transactional(function (EntityManagerInterface $em) use ($storageLocation) {
            $em->createQueryBuilder()->update(StorageLocation::class, 'l')->set('fslIsDefault', false);
            $em->persist($storageLocation);

            return $storageLocation;
        });
    }

    /**
     * Fetch a storage location by its ID
     * @param int $id
     * @return null|StorageLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function fetchByID($id)
    {
        return $this->entityManager->find(StorageLocation::class, (int)$id);
    }

    /**
     * Fetch a storage location by its name
     * @param string $name
     * @return null|StorageLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function fetchByName($name)
    {
        return $this->entityManager->getRepository(StorageLocation::class, 'l')->findBy(['fslName' => $name]);
    }

    /**
     * Fetch a list of storage locations
     * @return StorageLocation[]
     */
    public function fetchList()
    {
        $repository = $this->entityManager->getRepository(StorageLocation::class);
        return $repository->findBy([], ['fslID' => 'asc']);
    }

    /**
     * Fetch the default storage location
     * @return StorageLocation
     */
    public function fetchDefault()
    {
        $repository = $this->entityManager->getRepository(StorageLocation::class);
        return $repository->findOneBy([
            'fslIsDefault' => true
        ]);
    }

}
