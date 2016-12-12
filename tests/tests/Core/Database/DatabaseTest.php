<?php

class DatabaseTest extends ConcreteDatabaseTestcase
{
    protected $fixtures = array(
        'Users',
    );
    protected $tables = array(
        'User',
    );

    public function setUp()
    {
        $conn = $this->getConnection();
        $pdo = $conn->getConnection();

        $pdo->exec('DROP TABLE IF EXISTS Users');
        $pdo->exec(
            'CREATE TABLE Users (uID INT UNSIGNED NOT NULL AUTO_INCREMENT, uName VARCHAR(128) NULL, uFirstName VARCHAR(128) NULL, uEmail VARCHAR(128) NULL, PRIMARY KEY (uID));');
        parent::setUp();
    }

    public function testInvalidConnection()
    {
        $connection = Database::getFactory()->createConnection(
            array(
                'database' => md5(rand()),
                'user' => md5(rand()),
                'password' => md5(rand()),
                'host' => 'DB_SERVER',
            ));

        try {
            $errorCode = $connection->errorCode();
        } catch (PDOException $e) {
            return;
        }

        $this->fail('Invalid PDO exception not raised on failed connection.');
    }

    public function testValidConnection()
    {
        $config = \Config::get('database');
        $connection_config = $config['connections'][$config['default-connection']];
        $connection = Database::getFactory()->createConnection(
            array(
                'host' => $connection_config['server'],
                'user' => $connection_config['username'],
                'password' => $connection_config['password'],
                'database' => $connection_config['database'],
            ));

        try {
            $errorCode = $connection->errorCode();
            $this->assertTrue($errorCode == 0);
        } catch (PDOException $e) {
            $this->fail('Unable to connect to the database.');
        }
    }

    public function testActiveLazyLoadConnection()
    {
        $db = Database::get();
        $this->assertTrue($db instanceof \Concrete\Core\Database\Connection\Connection);
    }

    public function testLegacyLoaderDb()
    {
        $db = Loader::db();
        $this->assertTrue($db instanceof \Concrete\Core\Database\Connection\Connection);
    }

    public function testFetchRowsDoctrineAPI()
    {
        $db = Database::get();
        $r = $db->query('SELECT * FROM Users');
        $results = array();
        while ($row = $r->fetch()) {
            $results[] = $row;
        }
        $this->assertTrue(count($results) == 2);
        $this->assertTrue($results[0]['uName'] == 'admin');

        $uID = $db->fetchColumn('SELECT uID FROM Users WHERE uName = \'admin\'');
        $this->assertTrue($uID == 1);

        $uEmail = $db->fetchColumn('SELECT uEmail FROM Users WHERE uName = ?', array('admin'));
        $this->assertTrue($uEmail == 'andrew@concrete5.org');
    }

    public function testTableExists()
    {
        $db = Loader::db();
        $this->assertTrue($db->tableExists('users'));
        $this->assertTrue($db->tableExists('Users'));
        $this->assertFalse($db->tableExists('DummyTable'));
    }

    public function testLegacyConcreteApi()
    {
        $db = Loader::db();

        $q = "SELECT * FROM Users";
        $r = $db->Execute($q);
        $results = array();
        while ($row = $r->FetchRow()) {
            $results[] = $row['uName'];
        }

        $this->assertTrue($results[0] == 'admin');

        $row = $db->GetRow('SELECT uID, uName FROM Users WHERE uEmail = ?', array('testuser@concrete5.org'));
        $this->assertTrue($row['uID'] == 2 && $row['uName'] == 'testuser');

        $uName = $db->GetOne('SELECT uName FROM Users WHERE uID = ?', array(1));
        $this->assertTrue($uName == 'admin');

        $email = "testuser2@concrete5.org";
        $v = array('testuser2', $email);
        $q = "INSERT INTO Users (uName, uEmail) VALUES (?, ?)";
        $r = $db->prepare($q);
        $res = $db->execute($r, $v);
        $newUID = $db->Insert_ID();
        $this->assertTrue($newUID == 3);

        // sql protection
        $uName = 'testtesttest\' or uID = 1';
        $uID = $db->GetOne('SELECT uID FROM Users WHERE uName = ?', array($uName));
        $this->assertTrue($uID != 1);

        //numrows
        $r = $db->query('SELECT * FROM Users');
        $this->assertTrue($r->numRows() == 3);

        $v = array('testuser4', 'testuser4@concrete5.org');
        $q = "INSERT INTO Users (uName, uEmail) VALUES (?, ?)";
        $r = $db->query($q, $v);
        $newUID = $db->Insert_ID();
        $this->assertTrue($newUID == 4);

        // getcol
        $col = $db->GetCol('SELECT uID FROM Users');
        $this->assertTrue(count($col) == 4);
        for ($i = 0; $i < 4; ++$i) {
            $uID = $col[$i];
            $this->assertTrue(($i + 1) == $uID);
        }
    }

    public function testLegacyReplace()
    {
        $db = Database::get();
        $db->Replace('Users', array('uName' => 'testuser5', 'uEmail' => 'testuser5@concrete5.org'), array('uName'));
        $uID = $db->GetOne('SELECT uID FROM Users WHERE uEmail = ?', array('testuser5@concrete5.org'));
        $this->assertTrue($uID == 3);
        $row = $db->GetRow('SELECT uName, uEmail FROM Users WHERE uID = ?', array(3));
        $this->assertTrue($row['uName'] == 'testuser5');

        $db->Replace(
            'Users',
            array('uName' => 'testuser6', 'uEmail' => 'testuser6@concrete5.org'),
            array('uName', 'uEmail'));
        $row = $db->GetRow('SELECT uName, uEmail FROM Users WHERE uEmail = ?', array('testuser6@concrete5.org'));
        $this->assertTrue($row['uName'] == 'testuser6');

        $db->Replace('Users', array('uEmail' => 'andrew@concretecms.com', 'uName' => 'admin'), array('uName'));
        $row = $db->GetRow('SELECT uName, uID, uEmail FROM Users WHERE uID = ?', array(1));
        $this->assertTrue($row['uID'] == 1 && $row['uName'] == 'admin' && $row['uEmail'] == 'andrew@concretecms.com');
    }

    public function testQuoting()
    {
        $db = Database::get();
        $db->Replace('Users', array('uName' => "test'der", 'uEmail' => "testuser5'@concrete5.org"), array('uName'));
        $uName = $db->GetOne('SELECT uName FROM Users WHERE uEmail = ?', array("testuser5'@concrete5.org"));
        $this->assertTrue($uName == "test'der");
    }
}
