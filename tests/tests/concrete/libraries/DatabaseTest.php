<?php
class DatabaseTest extends PHPUnit_Extensions_Database_TestCase {
	protected $fixtures = array(
		'Users'
	);

	private $conn = null;

	public function setUp() {
		$conn = $this->getConnection();
		$pdo = $conn->getConnection();

		$pdo->exec('drop table if exists Users');
		$pdo->exec('create table Users (uID int unsigned not null auto_increment, uName varchar(128) null, uFirstName varchar(128) null, uEmail varchar(128) null, primary key (uID));');
		parent::setUp();
	}

	public function tearDown() {
		$allTables =
		$this->getDataSet($this->fixtures)->getTableNames();
		foreach ($allTables as $table) {
			// drop table
			$conn = $this->getConnection();
			$pdo = $conn->getConnection();
			$pdo->exec("DROP TABLE IF EXISTS `$table`;");
		}

		parent::tearDown();
	}

	public function getConnection() {
	    if ($this->conn === null) {
	        try {
	            $pdo = new PDO('mysql:host='. DB_SERVER.';dbname=' . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
	            $this->conn = $this->createDefaultDBConnection($pdo, 'test');
	        } catch (PDOException $e) {
	            echo $e->getMessage();
	        }
	    }
	    return $this->conn;
	}

	public function getDataSet($fixtures = array()) {
		if (empty($fixtures)) {
			$fixtures = $this->fixtures;
		}
		$compositeDs = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
		$fixturePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures';
	
		foreach ($fixtures as $fixture) {
			$path = $fixturePath . DIRECTORY_SEPARATOR . "$fixture.xml";
			$ds = $this->createMySQLXMLDataSet($path);
			$compositeDs->addDataSet($ds);
		}
		return $compositeDs;
	}

	public function testInvalidConnection() {
		$connection = Database::connect(array(
			'database' => md5(rand()),
			'user' => md5(rand()),
			'password' => md5(rand()),
			'host' => DB_SERVER
		));

		try {
			$errorCode = $connection->errorCode();
		} catch(PDOException $e) {
			return;
		}

		$this->fail('Invalid PDO exception not raised on failed connection.');
	}


	public function testValidConnection() {
		$connection = Database::connect(array(
			'database' => DB_DATABASE,
			'user' => DB_USERNAME,
			'password' => DB_PASSWORD,
			'host' => DB_SERVER
		));

		try {
			$errorCode = $connection->errorCode();
			$this->assertTrue($errorCode == 0);
		} catch(PDOException $e) {
			$this->fail('Unable to connect to the database.');
		}	
	}

	public function testActiveLazyLoadConnection() {
		$db = Database::get();
		$this->assertTrue($db instanceof \Concrete\Core\Database\Connection);
	}

	public function testLegacyLoaderDb() {
		$db = Loader::db();
		$this->assertTrue($db instanceof \Concrete\Core\Database\Connection);
	}

	public function testFetchRowsDoctrineAPI() {
		$db = Database::get();
		$r = $db->query('select * from Users');
		$results = array();
		while ($row = $r->fetch()) {
			$results[] = $row;
		}
		$this->assertTrue(count($results) == 2);
		$this->assertTrue($results[0]['uName'] == 'admin');

		$uID = $db->fetchColumn('select uID from Users where uName = \'admin\'');
		$this->assertTrue($uID == 1);

		$uEmail = $db->fetchColumn('select uEmail from Users where uName = ?', array('admin'));
		$this->assertTrue($uEmail == 'andrew@concrete5.org');
	}

	public function testLegacyConcreteApi() {
		
		$db = Loader::db();

		$q = "select * from Users";
		$r = $db->Execute($q);
		$results = array();
		while ($row = $r->FetchRow()) {
			$results[] = $row['uName'];
		}

		$this->assertTrue($results[0] == 'admin');


		$row = $db->GetRow('select uID, uName from Users where uEmail = ?', array('testuser@concrete5.org'));
		$this->assertTrue($row['uID'] == 2 && $row['uName'] == 'testuser');

		$uName = $db->GetOne('select uName from Users where uID = ?', array(1));
		$this->assertTrue($uName == 'admin');

		$email = "testuser2@concrete5.org";
		$v = array('testuser2', $email);
		$q = "insert into Users (uName, uEmail) values (?, ?)";
		$r = $db->prepare($q);
		$res = $db->execute($r, $v);
		$newUID = $db->Insert_ID();
		$this->assertTrue($newUID == 3);

		// sql protection
		$uName = 'testtesttest\' or uID = 1';
		$uID = $db->GetOne('select uID from Users where uName = ?', array($uName));
		$this->assertTrue($uID != 1);

		//numrows
		$r = $db->query('select * from Users');
		$this->assertTrue($r->numRows() == 3);

		$v = array('testuser4', 'testuser4@concrete5.org');
		$q = "insert into Users (uName, uEmail) values (?, ?)";
		$r = $db->query($q, $v);
		$newUID = $db->Insert_ID();
		$this->assertTrue($newUID == 4);
	}

	public function testLegacyReplace() {
		$db = Database::get();
		$db->Replace('Users', array('uName' => 'testuser5', 'uEmail'=> 'testuser5@concrete5.org'), array('uName'));
		$uID = $db->GetOne('select uID from Users where uEmail = ?', array('testuser5@concrete5.org'));
		$this->assertTrue($uID == 3);
		$row = $db->GetRow('select uName, uEmail from Users where uID = ?', array(3));
		$this->assertTrue($row['uName'] == 'testuser5');

		$db->Replace('Users', array('uName' => 'testuser6', 'uEmail'=> 'testuser6@concrete5.org'), array('uName', 'uEmail'));
		$row = $db->GetRow('select uName, uEmail from Users where uEmail = ?', array('testuser6@concrete5.org'));
		$this->assertTrue($row['uName'] == 'testuser6');

		$db->Replace('Users', array('uEmail' => 'andrew@concretecms.com', 'uName' => 'admin'), array('uName'));
		$row = $db->GetRow('select uName, uID, uEmail from Users where uID = ?', array(1));
		$this->assertTrue($row['uID'] == 1 && $row['uName'] == 'admin' && $row['uEmail'] == 'andrew@concretecms.com');
	}

	public function testQuoting() {
		$db = Database::get();
		$db->Replace('Users', array('uName' => "test'der", 'uEmail'=> "testuser5'@concrete5.org"), array('uName'));
		$uName = $db->GetOne('select uName from Users where uEmail = ?', array("testuser5'@concrete5.org"));
		$this->assertTrue($uName == "test'der");
	}



}