<?php
class ConcreteDatabaseTestCase extends PHPUnit_Extensions_Database_TestCase {

	private $conn = null;
	protected $tables = array();

	protected function appendXML($root, $new) {
		$node = $root->addChild($new->getName(), (string) $new);
		foreach($new->attributes() as $attr => $value) {
			$node->addAttribute($attr, $value);
		}
		foreach($new->children() as $ch) {
			$this->appendXML($node, $ch);
		}
	}

    protected function debug()
    {
        \Database::get()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

	public function getConnection() {
	    if ($this->conn === null) {
	        try {
	        	$db = Database::connect(
	        	array(
					'host' => DB_SERVER,
					'user' => DB_USERNAME,
					'password' => DB_PASSWORD,
					'database' => DB_DATABASE
				));
	            $this->conn = $this->createDefaultDBConnection($db->getWrappedConnection(), 'test');
	            $this->db = $db;
	        } catch (PDOException $e) {
	            echo $e->getMessage();
	        }
	    }
	    return $this->conn;
	}

	public function getDataSet($fixtures = array()) {
		$db = Database::get();
		if (count($this->tables)) {
			$partial = new SimpleXMLElement('<schema></schema>');
			$partial->addAttribute('version', '0.3');

			$xml = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');
			foreach($xml->table as $t) {
				$name = (string) $t['name'];
				if (in_array($name, $this->tables)) {
					$this->appendXML($partial, $t);
				}
			}

			$schema = \Concrete\Core\Database\Schema\Schema::loadFromXMLElement($partial, $db);
			$platform = $db->getDatabasePlatform();
			$queries = $schema->toSql($platform);
			foreach($queries as $query) {
				$db->query($query);
			}

		}

		if (empty($fixtures)) {
			$fixtures = $this->fixtures;
		}

		$reflectionClass = new ReflectionClass(get_called_class());
		$fixturePath = dirname($reflectionClass->getFilename()) . DIRECTORY_SEPARATOR . 'fixtures';
		$compositeDs = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
	
		foreach ($fixtures as $fixture) {
			$path = $fixturePath . DIRECTORY_SEPARATOR . "$fixture.xml";
			$ds = $this->createMySQLXMLDataSet($path);
			$compositeDs->addDataSet($ds);
		}
		return $compositeDs;
	}
	

	public function tearDown() {
		if (count($this->tables)) {
			foreach ($this->tables as $table) {
				// drop table
				$conn = $this->getConnection();
				$pdo = $conn->getConnection();
				$pdo->exec("DROP TABLE IF EXISTS `$table`;");
			}
		}

		$allTables =
		$this->getDataSet($this->fixtures)->getTableNames();
		foreach ($allTables as $table) {
			// drop table
			$conn = $this->getConnection();
			$pdo = $conn->getConnection();
			$pdo->exec("DROP TABLE IF EXISTS `$table`;");
		}

        $db = Loader::db();
        $db->getEntityManager()->clear();

        parent::tearDown();
	}



}