<?php

namespace Concrete\Tests\Database;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\EntityManagerFactory;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Database;
use PDOException;

class DatabaseTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [
        'Users',
    ];

    public function setUp(): void
    {
        $conn = $this->getConnection();

        $conn->exec('DROP TABLE IF EXISTS Users');
        $conn->exec(
            'CREATE TABLE Users (uID INT UNSIGNED NOT NULL AUTO_INCREMENT, uName VARCHAR(128) NULL, uFirstName VARCHAR(128) NULL, uEmail VARCHAR(128) NULL, PRIMARY KEY (uID));');
        parent::setUp();
    }

    public function testInvalidConnection()
    {
        $this->expectException(\Doctrine\DBAL\Driver\PDOException::class);
        // php 8.1 and above the error message is more detailed
        $this->expectExceptionMessageMatches('/(getaddrinfo (?:for .+ )?failed)/');
        $connection = Database::getFactory()->createConnection(
            [
                'database' => md5(mt_rand()),
                'user' => md5(mt_rand()),
                'password' => md5(mt_rand()),
                'host' => 'DB_SERVER',
            ]);
        $connection->errorCode();
    }

    public function testValidConnection()
    {
        $config = \Config::get('database');
        $connection_config = $config['connections'][$config['default-connection']];
        $connection = Database::getFactory()->createConnection(
            [
                'host' => $connection_config['server'],
                'user' => $connection_config['username'],
                'password' => $connection_config['password'],
                'database' => $connection_config['database'],
            ]);

        try {
            $errorCode = $connection->errorCode();
            $this->assertTrue($errorCode == 0);
        } catch (PDOException $e) {
            $this->fail('Unable to connect to the database.');
        }
    }

    public function testActiveLazyLoadConnection()
    {
        $db = Database::connection();
        $this->assertTrue($db instanceof \Concrete\Core\Database\Connection\Connection);
    }

    public function testLegacyLoaderDb()
    {
        $db = Database::connection();
        $this->assertTrue($db instanceof \Concrete\Core\Database\Connection\Connection);
    }

    public function testFetchRowsDoctrineAPI()
    {
        $db = Database::connection();
        $r = $db->executeQuery('SELECT * FROM Users');
        $results = [];
        while ($row = $r->fetch()) {
            $results[] = $row;
        }
        $this->assertTrue(count($results) == 2);
        $this->assertTrue($results[0]['uName'] == 'admin');

        $uID = $db->fetchColumn('SELECT uID FROM Users WHERE uName = \'admin\'');
        $this->assertTrue($uID == 1);

        $uEmail = $db->fetchColumn('SELECT uEmail FROM Users WHERE uName = ?', ['admin']);
        $this->assertTrue($uEmail == 'andrew@concrete5.org');
    }

    public function testTableExists()
    {
        $db = Database::connection();
        $this->assertTrue($db->tableExists('users'));
        $this->assertTrue($db->tableExists('Users'));
        $this->assertFalse($db->tableExists('DummyTable'));
    }


    public function testLegacyConcreteApi()
    {
        /** @var Connection $db */
        $db = Database::connection();

        $q = 'SELECT * FROM Users';
        $r = $db->Execute($q);
        $results = [];
        while ($row = $r->fetchAssociative()) {
            $results[] = $row['uName'];
        }

        $this->assertTrue($results[0] == 'admin');

        $row = $db->GetRow('SELECT uID, uName FROM Users WHERE uEmail = ?', ['testuser@concrete5.org']);
        $this->assertTrue($row['uID'] == 2 && $row['uName'] == 'testuser');

        $uName = $db->GetOne('SELECT uName FROM Users WHERE uID = ?', [1]);
        $this->assertTrue($uName == 'admin');

        $email = 'testuser2@concrete5.org';
        $v = ['testuser2', $email];
        $q = 'INSERT INTO Users (uName, uEmail) VALUES (?, ?)';
        $r = $db->prepare($q);
        $res = $db->execute($r, $v);
        $newUID = $db->Insert_ID();
        $this->assertTrue($newUID == 3);

        // sql protection
        $uName = 'testtesttest\' or uID = 1';
        $uID = $db->GetOne('SELECT uID FROM Users WHERE uName = ?', [$uName]);
        $this->assertTrue($uID != 1);

        //numrows
        $r = $db->query('SELECT * FROM Users');
        $this->assertTrue($r->numRows() == 3);

        $v = ['testuser4', 'testuser4@concrete5.org'];
        $q = 'INSERT INTO Users (uName, uEmail) VALUES (?, ?)';
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
        $db = Database::connection();
        $db->Replace('Users', ['uName' => 'testuser5', 'uEmail' => 'testuser5@concrete5.org'], ['uName']);
        $uID = $db->GetOne('SELECT uID FROM Users WHERE uEmail = ?', ['testuser5@concrete5.org']);
        $this->assertTrue($uID == 3);
        $row = $db->GetRow('SELECT uName, uEmail FROM Users WHERE uID = ?', [3]);
        $this->assertTrue($row['uName'] == 'testuser5');

        $db->Replace(
            'Users',
            ['uName' => 'testuser6', 'uEmail' => 'testuser6@concrete5.org'],
            ['uName', 'uEmail']);
        $row = $db->GetRow('SELECT uName, uEmail FROM Users WHERE uEmail = ?', ['testuser6@concrete5.org']);
        $this->assertTrue($row['uName'] == 'testuser6');

        $db->Replace('Users', ['uEmail' => 'andrew@concretecms.com', 'uName' => 'admin'], ['uName']);
        $row = $db->GetRow('SELECT uName, uID, uEmail FROM Users WHERE uID = ?', [1]);
        $this->assertTrue($row['uID'] == 1 && $row['uName'] == 'admin' && $row['uEmail'] == 'andrew@concretecms.com');
    }

    public function testQuoting()
    {
        $db = Database::connection();
        $db->Replace('Users', ['uName' => "test'der", 'uEmail' => "testuser5'@concrete5.org"], ['uName']);
        $uName = $db->GetOne('SELECT uName FROM Users WHERE uEmail = ?', ["testuser5'@concrete5.org"]);
        $this->assertTrue($uName == "test'der");
    }
    private function createPrimaryReplicaConnection(bool $keepReplica = false): Connection
    {
        $database = \Config::get('database');
        $config = $database['connections'][$database['default-connection']];
        $config['replica'] = [
            ['server' => $config['server'], 'replicaName' => 'test-reader'],
        ];
        $config['keepReplica'] = $keepReplica;

        /** @var Connection $connection */
        $connection = Database::getFactory()->createConnection($config);
        $connection->close();

        return $connection;
    }

    public function testFlatConfigurationUsesConcretePrimaryReplicaConnection()
    {
        $database = \Config::get('database');
        $config = $database['connections'][$database['default-connection']];

        $connection = Database::getFactory()->createConnection($config);
        $params = $connection->getParams();

        $this->assertInstanceOf(PrimaryReadReplicaConnection::class, $connection);
        $this->assertSame($params['primary'], $params['replica'][0]);

        $connection->close();
        $connection->executeQuery('SELECT 1')->fetchOne();
        $this->assertTrue($connection->isConnectedToPrimary());
        $connection->close();
    }

    public function testPrimaryAndReplicaConfigurationIsNormalized()
    {
        $database = \Config::get('database');
        $flatConfig = $database['connections'][$database['default-connection']];
        $config = [
            'driver' => $flatConfig['driver'],
            'primary' => [
                'host' => $flatConfig['server'],
                'user' => $flatConfig['username'],
                'password' => $flatConfig['password'],
                'dbname' => $flatConfig['database'],
            ],
            'replica' => [
                ['server' => $flatConfig['server']],
                ['host' => $flatConfig['server']],
            ],
        ];

        $connection = Database::getFactory()->createConnection($config);
        $params = $connection->getParams();

        $this->assertSame($flatConfig['server'], $params['primary']['host']);
        $this->assertSame($flatConfig['username'], $params['primary']['user']);
        $this->assertSame($flatConfig['database'], $params['primary']['database']);
        $this->assertSame($flatConfig['database'], $params['primary']['dbname']);
        $this->assertCount(2, $params['replica']);
        $this->assertSame($params['primary']['password'], $params['replica'][0]['password']);
        $this->assertSame($flatConfig['server'], $params['replica'][0]['host']);
        $connection->close();
    }

    public function testDbalReadWriteRoutingAndStickyPrimary()
    {
        $connection = $this->createPrimaryReplicaConnection();

        $connection->executeQuery('SELECT 1')->fetchOne();
        $this->assertFalse($connection->isConnectedToPrimary());

        $connection->executeStatement('SET @concrete_primary_replica_test = 1');
        $this->assertTrue($connection->isConnectedToPrimary());

        $connection->executeQuery('SELECT 1')->fetchOne();
        $this->assertTrue($connection->isConnectedToPrimary());
        $connection->close();
    }

    public function testLegacyApisAlwaysUsePrimary()
    {
        $connection = $this->createPrimaryReplicaConnection();
        $connection->query('SELECT 1');
        $this->assertTrue($connection->isConnectedToPrimary());
        $connection->close();

        $connection = $this->createPrimaryReplicaConnection();
        $connection->Execute('SELECT 1');
        $this->assertTrue($connection->isConnectedToPrimary());
        $connection->close();
    }

    public function testKeepReplicaAllowsExplicitSwitchBack()
    {
        $connection = $this->createPrimaryReplicaConnection(true);

        $connection->executeQuery('SELECT 1')->fetchOne();
        $this->assertFalse($connection->isConnectedToPrimary());

        $connection->executeStatement('SET @concrete_primary_replica_test = 1');
        $this->assertTrue($connection->isConnectedToPrimary());

        $connection->ensureConnectedToReplica();
        $this->assertFalse($connection->isConnectedToPrimary());
        $connection->close();
    }

    public function testDoctrineOrmUsesTheSameReadWriteRouting()
    {
        $connection = $this->createPrimaryReplicaConnection();
        $entityManager = app(EntityManagerFactory::class)->create($connection);

        $entityManager
            ->createQuery('SELECT u.uID FROM Concrete\\Core\\Entity\\User\\User u')
            ->setMaxResults(1)
            ->getScalarResult()
        ;
        $this->assertFalse($connection->isConnectedToPrimary());

        $entityManager
            ->createQuery('UPDATE Concrete\\Core\\Entity\\User\\User u SET u.uName = u.uName WHERE u.uID = -1')
            ->execute()
        ;
        $this->assertTrue($connection->isConnectedToPrimary());

        $entityManager
            ->createQuery('SELECT u.uID FROM Concrete\\Core\\Entity\\User\\User u')
            ->setMaxResults(1)
            ->getScalarResult()
        ;
        $this->assertTrue($connection->isConnectedToPrimary());

        $entityManager->close();
    }

}
