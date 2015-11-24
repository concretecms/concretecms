<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Database\EntityManagerFactoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Config;

/**
 * Class EntityManagerFactory
 * @package Concrete\Core\Express
 * The backend entity manager hooks into Doctrine and is called by the front-end
 * entity manager.
 */
class ObjectManager
{

    protected $application;
    protected $entityManager;
    protected $namespace;

    public function __construct(EntityManager $entityManager, Application $application)
    {
        $this->entityManager = $entityManager;
        $this->application = $application;
        $this->namespace = $application['config']->get('express.entity_classes.namespace');
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }


    public function create($entityName)
    {
        $class = '\\' . $this->getNamespace() . '\\' . $entityName;
        $entity = new $class();
        return $entity;
    }

    public function set(BaseEntity $entity, $field, $value)
    {
        $entity->setProperty($field, $value);
    }

    public function save(BaseEntity $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
