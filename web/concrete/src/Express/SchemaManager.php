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
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->tool = new SchemaTool($manager);
    }

    public function getCreateSql(Entity $entity)
    {
        $metadata = $this->manager->getClassMetadata($this->getClassName($entity));
        $generator->generate(array($metadata), $this->outputPath);

    }


}
