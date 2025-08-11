<?php

namespace Concrete\TestHelpers\Database;

use CacheLocal;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Tests\TestCase;
use Core;
use Doctrine\DBAL\Driver\Connection as PDOConnection;
use Doctrine\ORM\EntityManagerInterface;
use ORM;
use RuntimeException;
use SimpleXMLElement;

abstract class ConcreteDatabaseTestCase extends TestCase
{
    /**
     * The cached database connection.
     *
     * @var Connection
     */
    public static $connection = null;

    /**
     * Keys are tables that currently exist.
     *
     * @var bool[]
     */
    public static $existingTables = [];

    /**
     * Keys are entites that currently exist.
     *
     * @var bool[]
     */
    public static $existingEntites = [];

    /**
     * Table data cache.
     *
     * @var array[]
     */
    public static $tableData = [];

    /**
     * The tables to import from /concrete/config/db.xml.
     *
     * @var string[]
     */
    protected $tables = [];

    /**
     * The fixtures to import.
     *
     * @var string[]
     */
    protected $fixtures = [];

    /**
     * The Entities to import.
     *
     * @var string[]
     */
    protected $entityClassNames = [];

    /**
     * Set up before any tests run.
     */
    public static function setUpBeforeClass():void
    {
        Cache::disableAll();
        // Make sure tables are imported
        $testCase = new static();
        $testCase->importTables();
        $testCase->importMetadatas();
        // Call parent setup
        parent::setUpBeforeClass();
    }

    public function setUp():void
    {
        $this->importFixtures();
        parent::setUp();
    }

    /**
     * Tear down after class has completed.
     */
    public static function TearDownAfterClass():void
    {
        Cache::enableAll();
        // Make sure tables are removed
        $testCase = new static();
        $testCase->removeTables();

        // Call parent teardown
        parent::tearDownAfterClass();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        ORM::entityManager('core')->clear();
        Core::make('cache/request')->flush();
    }

    /**
     * Get the connection to use.
     *
     * @return \Concrete\Core\Database\Connection\Connection
     */
    protected function connection()
    {
        if (!static::$connection) {
            static::$connection = Core::make('database')->connection('ccm_test');
        }
        return static::$connection;
    }

    /**
     * Returns the test database connection.
     *
     * @throws \RuntimeException
     *
     * @return PDOConnection
     */
    protected function getConnection()
    {
        $connection = $this->connection()->getWrappedConnection();
        if (!$connection instanceof PDOConnection) {
            throw new RuntimeException('Invalid connection type.');
        }

        return $this->connection()->getWrappedConnection();
    }



    /**
     * Get the names of the tables to be imported from the xml files.
     *
     * @return string[]
     */
    protected function getTables()
    {
        return $this->tables;
    }

    /**
     * Import tables from $this->getTables().
     */
    protected function importTables()
    {
        $tables = [];
        foreach ($this->getTables() as $table) {
            if (!isset(static::$existingTables[$table]) || !in_array($table, $tables, true)) {
                $tables[] = $table;
            }
        }
        if ($tables === []) {
            return;
        }
        $connection = $this->connection();
        $importedTables = [];
        $xml = $this->extractTableData($tables, $importedTables);
        $this->importTableXML($xml, $connection);
        foreach ($importedTables as $table) {
            static::$existingTables[$table] = true;
        }
        foreach ([
            'btCoreStackDisplay' => 'core_scrapbook_display'
        ] as $blockTypeTable => $blockTypeHandle) {
            if (!in_array($blockTypeTable, $tables, true)) {
                continue;
            }
            $xml = simplexml_load_file(DIR_BASE_CORE . '/blocks/' . $blockTypeHandle .'/db.xml');
            $this->importTableXML($xml, $connection);
            static::$existingTables[$blockTypeTable] = true;
            $importedTables[] = $blockTypeTable;
        }
        $invalidTables = array_diff($tables, $importedTables);
        if ($invalidTables !== []) {
            throw new RuntimeException("Unrecognized tables to be created:\n- " . implode("\n- ", $invalidTables));
        }
    }

    /**
     * Remove all existing tables.
     */
    protected function removeTables()
    {
        $connection = $this->connection();

        // Get all existing tables
        $tables = $connection->query('show tables')->fetchAllAssociative();
        $tables = array_map(function($tableSet) {
            return array_shift($tableSet);
        }, $tables);

        // Turn off foreign key checks
        $connection->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            // Drop tables
            $connection->query("DROP TABLE `{$table}`");
        }

        // Reset foreign key checks on
        $connection->query('SET FOREIGN_KEY_CHECKS = 1');

        // Clear exists cache
        static::$existingTables = [];
        static::$existingEntites = [];
    }

    /**
     * Extract the table data from the db.xml.
     *
     * @param string[] $tables the wanted table names
     * @param string[] $importedTables imported tables will be appended to this argument
     */
    protected function extractTableData(array $tables, array &$importedTables): SimpleXMLElement
    {
        if ($tables === []) {
            return null;
        }
        $partial = new SimpleXMLElement('<schema xmlns="http://www.concrete5.org/doctrine-xml/0.5" />');
        $xml1 = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');
        foreach ($xml1->table as $table) {
            $name = (string) $table['name'];
            $index = array_search($name, $tables, true);
            if ($index === false) {
                continue;
            }
            $this->appendXML($partial, $table);
            $importedTables[] = $name;
            unset($tables[$index]);
            if ($tables === []) {
                break;
            }
        }

        return $partial;
    }

    /**
     * Import needed tables.
     *
     * @param SimpleXMLElement $xml
     * @param Connection $connection
     *
     * @internal param $partial
     */
    protected function importTableXML(SimpleXMLElement $xml, Connection $connection)
    {
        // Convert the given partial into sql create statements
        $schema = Schema::loadFromXMLElement($xml, $connection);
        $queries = $schema->toSql($connection->getDatabasePlatform());

        // Run queries
        foreach ($queries as $query) {
            $connection->query($query);
        }
    }


    protected function importFixtures()
    {
        $fixtures = $this->fixtures;
        if (!empty($fixtures)) {
            $testClass = get_called_class();
            if (strpos($testClass, 'Concrete\\Tests\\') !== 0) {
                throw new RuntimeException('Invalid test case class name: ' . $testClass);
            }
            $namespaceChunks = explode('\\', $testClass);
            $fixturePath = DIR_TESTS . '/assets/' . $namespaceChunks[2];

            foreach ((array) $fixtures as $fixture) {
                $path = $fixturePath . "/$fixture.xml";
                $xml = simplexml_load_file($path);
                if ($xml) {
                    $this->importTableDataXML($xml, $this->connection());
                }
            }
        }
    }

    protected function importTableDataXml(\SimpleXMLElement $xml, Connection $connection)
    {
        if ($xml->database && $xml->database->table_data) {
            foreach ($xml->database->table_data as $tableData) {
                $name = $tableData['name']->__toString();
                $connection->executeQuery("DELETE FROM " .$connection->quoteIdentifier($name));
                foreach ($tableData->row as $rowData) {
                    $queryBuilder = $connection->createQueryBuilder();
                    $queryBuilder->insert($name);
                    foreach ($rowData->field as $field) {
                        $queryBuilder->setValue($field['name']->__toString(), ':'.$field['name']->__toString());
                        $queryBuilder->setParameter(':'.$field['name']->__toString(),$field->__toString());
                    }
                    $queryBuilder->execute();
                }
            }
        }
    }

    /**
     * Get the entities to import.
     *
     * @return string
     */
    protected function getEntityClassNames(): array
    {
        return $this->entityClassNames;
    }

    /**
     * Import requested metadatas.
     */
    protected function importMetadatas()
    {
        $sm = Core::make(DatabaseStructureManager::class);
        $metadatas = $this->getMetadatas();
        if ($metadatas) {
            $sm->installDatabaseFor($metadatas);
        }
    }

    /**
     * Gets the metadatas to import.
     *
     * @return \Doctrine\Persistence\Mapping\ClassMetadata[]
     */
    protected function getMetadatas()
    {
        $install = array_values(
            array_unique(
                array_map(
                    static function (string $entityClassName): string {
                        return ltrim($entityClassName, '\\');
                    },
                    $this->getEntityClassNames()
                )
            )
        );
        $metadatas = [];
        if ($install !== []) {
            $manager = app(EntityManagerInterface::class);
            $factory = $manager->getMetadataFactory();
            foreach ($factory->getAllMetadata() as $meta) {
                $index = array_search($meta->getName(), $install, true);
                if ($index === false) {
                    continue;
                }
                unset($install[$index]);
                if (!isset(self::$existingEntites[$meta->getName()])) {
                    self::$existingEntites[$meta->getName()] = true;
                    $metadatas[] = $meta;
                }
                if ($install === []) {
                    break;
                }
            }
        }
        if ($install !== []) {
            throw new RuntimeException("Unrecognized entities to be installed:\n- " . implode("\n- ", $install));
        }

        return $metadatas;
    }

    /**
     * Append an xml onto another xml.
     *
     * @param \SimpleXMLElement $root
     * @param \SimpleXMLElement $new
     */
    protected function appendXML(SimpleXMLElement $root, SimpleXMLElement $new)
    {
        $node = $root->addChild($new->getName(), (string) $new);

        foreach ($new->attributes() as $attr => $value) {
            $node->addAttribute($attr, $value);
        }

        foreach ($new->children() as $ch) {
            $this->appendXML($node, $ch);
        }
    }

    protected function debug()
    {
        $this->connection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

    /**
     * Truncate all known databases.
     *
     * @param null|string[] $tables The tables to truncate
     */
    protected function truncateTables($tables = null)
    {
        $connection = $this->connection();

        if ($tables === null) {
            // Get all existing tables
            $tables = $connection->query('show tables')->fetchAllAssociative();
            $tables = array_map(function ($table) {
                return array_shift($table);
            }, $tables);
        }

        // Turn off foreign key checks
        $connection->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            // Drop tables
            $connection->exec("TRUNCATE TABLE `{$table}`");
        }

        // Reset foreign key checks on
        $connection->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}
