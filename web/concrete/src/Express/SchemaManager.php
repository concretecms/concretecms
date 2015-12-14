<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Database\EntityManagerFactoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\DBAL\Schema\SchemaDiff;
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

    public function synchronizeDatabase(Entity $entity)
    {
        $metadata = $this->entityManager->getClassMetadata($this->factory->getClassName($entity));
        $connection = $this->entityManager->getConnection();
        $manager = $connection->getSchemaManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $fromSchema = $manager->createSchema();
        $newSchema = $tool->getSchemaFromMetadata(array($metadata));
        $newTables = array();
        $changedTables = array();
        foreach ($newSchema->getTables() as $newTable) {
            // Check if the table already exists
            if ($fromSchema->hasTable($newTable->getName())) {
                $diff = $comparator->diffTable($fromSchema->getTable($newTable->getName()), $newTable);
                if ($diff) {
                    $changedTables[] = $diff;
                }
            } else {
                $newTables[] = $newTable;
            }
        }
        if (count($newTables) > 0 || count($changedTables) > 0) {
            $schemaDiff = new SchemaDiff($newTables, $changedTables);
            $platform = $connection->getDatabasePlatform();
            $migrateSql = $schemaDiff->toSql($platform);
            foreach ($migrateSql as $sql) {
                $connection->executeQuery($sql);
            }
        }
    }


}
