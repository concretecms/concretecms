<?php

namespace Concrete\Core\Database;

use Concrete\Core\Database\EntityManager\Driver\DriverInterface;
use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManager;

class EntityManagerConfigUpdater
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getDriverChain()
    {
        $configuration = $this->entityManager->getConfiguration();
        $driverChain = $configuration->getMetadataDriverImpl();
        return $driverChain;
    }

    public function addDriver(DriverInterface $driver)
    {
        $this->getDriverChain()->addDriver($driver->getDriver(), $driver->getNamespace());
    }

}
