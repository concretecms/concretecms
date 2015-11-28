<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Database\EntityManagerFactoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Config;

/**
 * Decorator for the doctrine schema tool.
 */
class SchemaManager
{

    protected $tool;
    protected $factory;

    public function __construct(BackendEntityManagerFactory $factory)
    {
        $this->factory = $factory;
        $this->entityManager = $factory->create(\Database::connection());
        $this->tool = new SchemaTool($this->entityManager);
    }

    public function getCreateSql(Entity $entity)
    {
        $metadata = $this->entityManager->getClassMetadata($this->factory->getClassName($entity));
        return $this->tool->getCreateSchemaSql(array($metadata));

    }


}
