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

    public function addDriver(DriverInterface $driver)
    {
        $configuration = $this->entityManager->getConfiguration();
        $driverChain = $configuration->getMetadataDriverImpl();
        print_r($driverChain);exit;

    }

}
