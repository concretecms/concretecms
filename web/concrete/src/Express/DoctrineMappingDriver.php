<?php

namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class DoctrineMappingDriver implements MappingDriver
{
    protected $rootEntityManager;
    protected $classCache;
    protected $application;
    protected $namespace;

    public function __construct(Application $application, EntityManagerInterface $rootEntityManager)
    {
        $this->application = $application;
        $this->rootEntityManager = $rootEntityManager;
    }

    protected function initialize()
    {
        $r = $this->rootEntityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entities = $r->findAll();
        foreach($entities as $entity) {
            $className = '';
            if (isset($this->namespace)) {
                $className .= trim($this->namespace, '\\') . '\\';
            }
            $className .= $entity->getName();
            $this->classCache[$className] = $entity;
        }
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


    public function loadMetadataForClass($className, \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata)
    {
        if (!isset($this->classCache)) {
            $this->initialize();
        }
        /** @var $entity \Concrete\Core\Entity\Express\Entity */
        $entity = $this->classCache[$className];
        $populator = new MetadataPopulator($metadata, $entity);
        $populator->setTablePrefix($this->application['config']->get('express.database.table_prefix'));
        $populator->populate();
    }


    public function getAllClassNames()
    {
        if (!isset($this->classCache)) {
            $this->initialize();
        }

        return array_keys($this->classCache);
    }

    public function isTransient($className)
    {
        if (!isset($this->classCache)) {
            $this->initialize();
        }

        if (in_array($className, array_keys($this->classCache))) {
            return true;
        }

        return false;
    }


}