<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entity;
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
        $writer = $this->application->make('express.writer');
        $writer->writeClass($entity);

        $factory = new BackendEntityManagerFactory($this->application, $this->entityManager);
        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $manager->synchronizeDatabase($entity);
    }

}