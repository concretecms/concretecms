<?php

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\DatabaseStructureManager;

class ConcreteDatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    /** @var PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection */
    private static $conn = null;
    private static $db = null;
    protected $tables = array();
    protected $fixtures = array();

    /*
    protected function setUp() {
        parent::setUp();
    }
    */

    protected function appendXML($root, $new)
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
        \Database::get()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

    public function getConnection()
    {
        if (self::$conn === null) {
            $config = \Config::get('database');
            $connection_config = $config['connections'][$config['default-connection']];
            $db = Database::getFactory()->createConnection(
            array(
                'host' => $connection_config['server'],
                'user' => $connection_config['username'],
                'password' => $connection_config['password'],
                'database' => $connection_config['database'],
            ));
            self::$conn = $this->createDefaultDBConnection($db->getWrappedConnection(), 'test');
            self::$db = $db;
        }

        return self::$conn;
    }

    public function getDataSet($fixtures = array())
    {
        $db = Database::get();
        if (count($this->tables)) {
            $partial = new SimpleXMLElement('<schema xmlns="http://www.concrete5.org/doctrine-xml/0.5" />');

            $xml = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');
            foreach ($xml->table as $t) {
                $name = (string) $t['name'];
                if (in_array($name, $this->tables)) {
                    $this->appendXML($partial, $t);
                }
            }

            $schema = \Concrete\Core\Database\Schema\Schema::loadFromXMLElement($partial, $db);
            $platform = $db->getDatabasePlatform();
            $queries = $schema->toSql($platform);
            foreach ($queries as $query) {
                $db->query($query);
            }

            // Install entity database tables
            $app = Application::getFacadeApplication();
            $em = $app->make('Doctrine\ORM\EntityManager');
            $dbm = new DatabaseStructureManager($em);

            $metadatas = $dbm->getMetadatas();
            $installMetadatas = array();
            foreach ($metadatas as $data) {
                if (in_array($data->getTableName(), $this->tables)) {
                    $installMetadatas[] = $data;
                }
            }
            if (count($installMetadatas) > 0) {
                $dbm->installDatabaseFor($installMetadatas);
            }
        }

        if (empty($fixtures)) {
            $fixtures = $this->fixtures;
        }

        $reflectionClass = new ReflectionClass(get_called_class());
        $fixturePath = dirname($reflectionClass->getFilename()) . DIRECTORY_SEPARATOR . 'fixtures';
        $compositeDs = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());

        foreach ((array) $fixtures as $fixture) {
            $path = $fixturePath . DIRECTORY_SEPARATOR . "$fixture.xml";
            $ds = $this->createMySQLXMLDataSet($path);
            $compositeDs->addDataSet($ds);
        }
        if (in_array('BlockTypes', $this->tables)) {
            $xml = simplexml_load_file(DIR_BASE_CORE . '/blocks/core_scrapbook_display/db.xml');
            $schema = \Concrete\Core\Database\Schema\Schema::loadFromXMLElement($xml, $db);
            $platform = $db->getDatabasePlatform();
            $queries = $schema->toSql($platform);
            foreach ($queries as $query) {
                $db->query($query);
            }
        }

        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');
        $metadatas = $this->getMetaDatas();
        if (count($metadatas)) {
            $sm->installDatabaseFor($metadatas);
        }

        return $compositeDs;
    }

    protected function getMetaDatas()
    {
        $metadatas = array();
        if (isset($this->metadatas) && is_array($this->metadatas)) {
            $em = self::$db->getEntityManager();
            $cmf = $em->getMetadataFactory();
            foreach ($cmf->getAllMetadata() as $meta) {
                if (in_array($meta->getName(), $this->metadatas)) {
                    $metadatas[] = $meta;
                }
            }
        }
        return $metadatas;
    }

    public function tearDown()
    {
        if (count($this->tables)) {
            if (in_array('BlockTypes', $this->tables)) {
                $this->tables[] = 'btCoreScrapbookDisplay';
            }
            $conn = $this->getConnection();
            $pdo = $conn->getConnection();
            $pdo->exec('set foreign_key_checks = 0');
            foreach ($this->tables as $table) {
                // drop table
                $conn = $this->getConnection();
                $pdo = $conn->getConnection();
                $pdo->exec("DROP TABLE IF EXISTS `$table`;");
            }
            $pdo->exec('set foreign_key_checks = 1');
        }

        $allTables = $this->getDataSet($this->fixtures)->getTableNames();
        foreach ($allTables as $table) {
            // drop table
            $conn = $this->getConnection();
            $pdo = $conn->getConnection();
            $pdo->exec("DROP TABLE IF EXISTS `$table`;");
        }

        \ORM::entityManager('core')->clear();

        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');
        $metadatas = $this->getMetaDatas();
        if (count($metadatas)) {
            $sm->uninstallDatabaseFor($metadatas);
        }


        \CacheLocal::flush();

        parent::tearDown();
    }
}
