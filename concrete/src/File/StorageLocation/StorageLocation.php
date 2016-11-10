<?php
namespace Concrete\Core\File\StorageLocation;

use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Concrete\Core\Support\Facade\Application;
use Database;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class StorageLocation
 * @deprecated Functionality moved to StorageLocationFactory
 * @package Concrete\Core\File\StorageLocation
 */
class StorageLocation
{

    /**
     * @deprecated use:
     *     $location = $storageLocationFactory->create($configuration, $fslName);
     *     $storageLocationFactory->persist($location);
     *
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @param string $fslName
     * @param bool $fslIsDefault
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    public static function add(ConfigurationInterface $configuration, $fslName, $fslIsDefault = false)
    {
        $app = Application::getFacadeApplication();
        /** @var StorageLocationFactory $factory */
        $factory = $app[StorageLocationFactory::class];

        $location = $factory->create($configuration, $fslName, $fslIsDefault);
        $location->setIsDefault($fslIsDefault);

        return $factory->persist($location);
    }

    /**
     * @deprecated use FileStorageFactory::fetchByID()
     * @param int $id
     * @return null|StorageLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function getByID($id)
    {
        $app = Application::getFacadeApplication();
        return $app[StorageLocationFactory::class]->fetchByID($id);
    }

    /**
     * @deprecated use FileStorageFactory::fetchList()
     * @return StorageLocation[]
     */
    public static function getList()
    {
        $app = Application::getFacadeApplication();
        return $app[StorageLocationFactory::class]->fetchList();
    }

    /**
     * @deprecated use StorageLocationFactory::fetchDefault()
     * @return StorageLocation
     */
    public static function getDefault()
    {
        $app = Application::getFacadeApplication();
        return $app[StorageLocationFactory::class]->fetchDefault();
    }


}
