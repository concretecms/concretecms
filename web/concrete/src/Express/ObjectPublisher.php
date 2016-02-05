<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManagerFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Support\Facade\DatabaseORM;
use Doctrine\ORM\EntityManager;

class ObjectPublisher
{
    protected $application;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, Application $application)
    {
        $this->entityManager = $entityManager;
        $this->application = $application;
    }

    public function publish(Entity $entity)
    {
        $cacheDriver = $this->entityManager->getConfiguration()->getMetadataCacheImpl();
        if ($cacheDriver) {
            $cacheDriver->deleteAll();
        }

        $factory = new EntityManagerFactory();
        $connection = $this->entityManager->getConnection();
        $entityManager = $factory->create($connection);

        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $destPath = $entityManager->getConfiguration()->getProxyDir();
        $entityManager->getProxyFactory()->generateProxyClasses($metadatas, $destPath);

        $writer = $this->application->make('express.writer');
        $writer->writeClass($entity);

        $factory = new BackendEntityManagerFactory($this->application, $this->entityManager);
        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $manager->synchronizeDatabase($entity);
    }
}
