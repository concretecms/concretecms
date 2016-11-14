<?php

namespace Concrete\Core\File\StorageLocation;

use Concrete\Core\Database\DatabaseManagerORM;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation as StorageLocationEntity;
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
     * @return StorageLocationEntity
     */
    public function create(ConfigurationInterface $configuration, $name)
    {
        $location = new StorageLocationEntity();
        $location->setConfigurationObject($configuration);
        $location->setName($name);

        return $location;
    }

    /**
     * Store a created storage location to the database
     * @param StorageLocationEntity $storageLocation
     * @return StorageLocationEntity The persisted location, may not be the same as the passed in object
     */
    public function persist(StorageLocationEntity $storageLocation)
    {
        return $this->entityManager->transactional(function (EntityManagerInterface $em) use ($storageLocation) {
            $em->createQueryBuilder()->update(StorageLocationEntity::class, 'l')->set('fslIsDefault', false);
            $em->persist($storageLocation);

            return $storageLocation;
        });
    }

    /**
     * Fetch a storage location by its ID
     * @param int $id
     * @return null|StorageLocationEntity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function fetchByID($id)
    {
        return $this->entityManager->find(StorageLocationEntity::class, (int)$id);
    }

    /**
     * Fetch a storage location by its name
     * @param string $name
     * @return null|StorageLocationEntity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function fetchByName($name)
    {
        return $this->entityManager->getRepository(StorageLocationEntity::class, 'l')->findBy(['fslName' => $name]);
    }

    /**
     * Fetch a list of storage locations
     * @return StorageLocationEntity[]
     */
    public function fetchList()
    {
        $repository = $this->entityManager->getRepository(StorageLocationEntity::class);
        return $repository->findBy([], ['fslID' => 'asc']);
    }

    /**
     * Fetch the default storage location
     * @return StorageLocationEntity
     */
    public function fetchDefault()
    {
        $repository = $this->entityManager->getRepository(StorageLocationEntity::class);
        return $repository->findOneBy([
            'fslIsDefault' => true
        ]);
    }

}
