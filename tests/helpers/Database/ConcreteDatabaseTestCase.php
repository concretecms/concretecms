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
    protected $metadatas = [];

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
        $connection = $this->connection();

        // Filter out any tables that have already been imported
        $tables = array_filter($this->getTables(), function ($table) {
            return !isset(static::$existingTables[$table]);
        });

        if ($tables) {
            $xml = $this->extractTableData($tables);
            // Try to extract the tables
            if (!$xml) {
                throw new RuntimeException('Invalid tables: ' . json_encode($tables));
            }

            // Import any extracted tables
            $this->importTableXML($xml, $connection);

            // Check for special `BlockType` case
            if (in_array('BlockTypes', $tables, false)) {
                $xml = simplexml_load_file(DIR_BASE_CORE . '/blocks/core_scrapbook_display/db.xml');
                $this->importTableXML($xml, $connection);
            }
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
     * @param array $tables
     *
     * @return array|null
     */
    protected function extractTableData(array $tables)
    {
        // If there are no tables, there's no reason to scan the XML
        if (!count($tables)) {
            return null;
        }

        // Initialize an xml document
        $partial = new SimpleXMLElement('<schema xmlns="http://www.concrete5.org/doctrine-xml/0.5" />');

        // Open the db.xml file
        $xml1 = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');
        $importedTables = [];

        // Loop through tables that exist in the document
        foreach ($xml1->table as $table) {
            $name = (string) $table['name'];

            // If this table is being requested
            if (in_array($name, $tables, false)) {
                $this->appendXML($partial, $table);

                // Remove the table from our list of tables
                $tables = array_filter($tables, function ($name) use ($table) {
                    return $name !== $table;
                });

                // Track that we actually have tables to import
                $importedTables[] = $name;

                static::$existingTables[$name] = true;
            }

            if (!$tables) {
                break;
            }
        }

        // Return the partial only if there are tables to import
        return $importedTables ? $partial : null;
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
     * @return array
     */
    protected function getMetadatas()
    {
        $metadatas = [];
        $install = $this->metadatas;

        // If there are metadatas to import
        if ($this->metadatas && is_array($this->metadatas)) {
            /** @var EntityManagerInterface $manager */
            $manager = Core::make(EntityManagerInterface::class);
            $factory = $manager->getMetadataFactory();

            // Loop through all metadata
            foreach ($factory->getAllMetadata() as $meta) {
                if (!isset(self::$existingEntites[$meta->getName()]) && in_array($meta->getName(), $install, false)) {
                    $metadatas[] = $meta;

                    // Remove this from the list of entities to install
                    $install = array_filter($install, function ($name) use ($meta) {
                        return $name !== $meta->getName();
                    });

                    // Track that we've created this metadata
                    self::$existingEntites[$meta->getName()] = true;
                }

                // If no more entities to install, lets break
                if (!$install) {
                    break;
                }
            }
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
