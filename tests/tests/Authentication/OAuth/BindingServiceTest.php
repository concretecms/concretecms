<?php
namespace Concrete\Tests\Authentication\OAuth;

use Concrete\Core\Authentication\Type\OAuth\BindingService;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Database\Driver\PDOStatement;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Mockery as M;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

class BindingServiceTest extends PHPUnit_Framework_TestCase
{

    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testClearBindingWithBadConnection()
    {
        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = $this->createFakeConnection();
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        /** @var BindingService|M\Mock $service */
        $service = new BindingService($fakeDatabaseManager);

        $this->setExpectedException(\RuntimeException::class, 'Unable to delete binding.');
        $service->clearBinding(1, null, 'test');
    }

    public function testClearBindingLogging()
    {
        $fakeLogger = M::mock(LoggerInterface::class);

        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = $this->createFakeConnection();
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        // Set up fake bindings
        // First it checks for an existing user associated with the binding, we're just passing null so we should just return null
        $fakeResult = M::mock(PDOStatement::class);
        $fakeResult->shouldReceive('fetchColumn')->andReturn('35');
        $fakeConnection->shouldReceive('executeQuery')
            ->with('SELECT user_id FROM OauthUserMap WHERE (namespace = :namespace) AND (binding = :binding)', ['binding' => 'testing', 'namespace' => 'test'], [])
            ->andReturn($fakeResult);

        // Second it checks for bindings associated with the user, we want to return a fake binding here
        $fakeResult = M::mock(PDOStatement::class);
        $fakeResult->shouldReceive('fetchColumn')->andReturn('foo');
        $fakeConnection->shouldReceive('executeQuery')
            ->with('SELECT binding FROM OauthUserMap WHERE (namespace = :namespace) AND (user_id = :id)', ['id' => 1, 'namespace' => 'test'], [])
            ->andReturn($fakeResult);

        // Next we have to support the secondary call with both matching
        $fakeResult = M::mock(PDOStatement::class);
        $fakeResult->shouldReceive('fetchColumn')->andReturn('44');
        $fakeConnection->shouldReceive('executeQuery')
            ->with('SELECT user_id FROM OauthUserMap WHERE (namespace = :namespace) AND (binding = :binding)', ['binding' => 'testing2', 'namespace' => 'test'], [])
            ->andReturn($fakeResult);
        $fakeResult = M::mock(PDOStatement::class);
        $fakeResult->shouldReceive('fetchColumn')->andReturn('testing2');
        $fakeConnection->shouldReceive('executeQuery')
            ->with('SELECT binding FROM OauthUserMap WHERE (namespace = :namespace) AND (user_id = :id)', ['id' => 44, 'namespace' => 'test'], [])
            ->andReturn($fakeResult);

        /** @var BindingService|M\Mock $service */
        $service = new BindingService($fakeDatabaseManager);
        $service->setLogger($fakeLogger);

        // Setup logging expectation
        // We told the binding service user 1 was bound to 'foo'
        $fakeLogger->shouldReceive('warning')->once()->with(
            'Deleting user binding: User #{user} was bound to "{binding}" in "{namespace}".',
            ['user' => 1, 'binding' => 'foo', 'namespace' => 'test', 'matchBoth' => false]);

        // We told the binding service 'testing' was bound to user 35.
        $fakeLogger->shouldReceive('warning')->once()->with(
            'Deleting user binding: User #{user} was bound to "{binding}" in "{namespace}".',
            ['user' => 35, 'binding' => 'testing', 'namespace' => 'test', 'matchBoth' => false]);

        // And finally in the last test we tell the binding service 44 is bound to testing2
        $fakeLogger->shouldReceive('warning')->once()->with(
            'Deleting user binding: User #{user} was bound to "{binding}" in "{namespace}".',
            ['user' => 44, 'binding' => 'testing2', 'namespace' => 'test', 'matchBoth' => true]);

        try {
            $service->clearBinding(1, 'testing', 'test');
        } catch (\RuntimeException $e) {
            // Ignore error, we aren't expecting this to succeed, it should only generate logs
        }

        // Test exact matches
        try {
            $service->clearBinding(44, 'testing2', 'test', true);
        } catch (\RuntimeException $e) {
            // Ignore error, we aren't expecting this to succeed, it should only generate logs
        }
    }

    public function testClearBinding()
    {
        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = $this->createFakeConnection();
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        $fakeResult = M::mock(PDOStatement::class);
        $fakeResult->shouldReceive('fetchColumn')->andReturn(1);
        $service = new BindingService($fakeDatabaseManager);

        // Make sure we attempt to delete with "AND"
        $fakeConnection->shouldReceive('executeUpdate')->once()
            ->with('DELETE FROM OauthUserMap WHERE (namespace = :namespace) AND ((user_id = :id) OR (binding = :binding))', [
                'namespace' => 'test',
                'binding' => 'foo',
                'id' => 1
            ], [])
            ->andReturn($fakeResult);
        $this->assertEquals(1, $service->clearBinding(1, 'foo', 'test'));

        // Make sure we attempt to delete with "OR"
        $fakeConnection->shouldReceive('executeUpdate')->once()
            ->with('DELETE FROM OauthUserMap WHERE (namespace = :namespace) AND ((user_id = :id) AND (binding = :binding))', [
                'namespace' => 'test',
                'binding' => 'foo',
                'id' => 1
            ], [])
            ->andReturn(1);
        $this->assertEquals(1, $service->clearBinding(1, 'foo', 'test', true));
    }

    public function testBindInvalidUser()
    {
        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = $this->createFakeConnection();
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        $service = new BindingService($fakeDatabaseManager);

        $this->setExpectedException(\InvalidArgumentException::class, 'Invalid user id provided');
        $service->bindUserId('foo', 'testing', 'test');
    }

    public function testBindUserId()
    {
        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = M::mock(Connection::class);
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        $service = M::mock(BindingService::class)->makePartial();
        $service->__construct($fakeDatabaseManager);

        // Make sure we attempt to clear bindings
        $service->shouldReceive('clearBinding')->once()->with(43, 'testing', 'test');

        // Make sure we try to insert the right stuff
        $fakeConnection->shouldReceive('insert')->once()->with('OauthUserMap', [
            'user_id' => 43,
            'binding' => 'testing',
            'namespace' => 'test',
        ])->andReturn(1);

        // Make sure our logs get logged
        $logger = M::mock(LoggerInterface::class);
        $service->setLogger($logger);

        $logger->shouldReceive('warning')->once()->with('Bound user: User #{user} is now bound to "{binding}" in "{namespace}".', [
            'user' => 43,
            'binding' => 'testing',
            'namespace' => 'test',
        ]);

        $service->bindUserId(43, 'testing', 'test');
    }

    public function testGetBoundUserID()
    {
        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = $this->createFakeConnection();
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        $service = new BindingService($fakeDatabaseManager);

        $result = M::mock(PDOStatement::class);
        $result->shouldReceive('fetchColumn')->andReturn('1414');
        $fakeConnection->shouldReceive('executeQuery')
            ->with('SELECT user_id FROM OauthUserMap WHERE (namespace = :namespace) AND (binding = :binding)', [
                'namespace' => 'test',
                'binding' => 'foo',
            ], [])
            ->andReturn($result);

        $this->assertEquals(1414, $service->getBoundUserId('foo', 'test'));
    }

    public function testGetUserBinding()
    {
        $fakeDatabaseManager = M::mock(DatabaseManager::class);
        $fakeConnection = $this->createFakeConnection();
        $fakeDatabaseManager->shouldReceive('connection')->andReturn($fakeConnection);

        $service = new BindingService($fakeDatabaseManager);

        $result = M::mock(PDOStatement::class);
        $result->shouldReceive('fetchColumn')->andReturn('foo');
        $fakeConnection->shouldReceive('executeQuery')
            ->with('SELECT binding FROM OauthUserMap WHERE (namespace = :namespace) AND (user_id = :id)', [
                'namespace' => 'test',
                'id' => 1414,
            ], [])
            ->andReturn($result);

        $this->assertEquals('foo', $service->getUserBinding(1414, 'test'));
    }

    public function testBindUser()
    {
        /** @var BindingService|M\Mock $service */
        $service = M::mock(BindingService::class)->makePartial();
        $fakeUser = M::mock(User::class, ['getUserID' => 1337]);
        $service->shouldReceive('bindUserId')->once()->with(1337, 'foo', 'test');
        $service->bindUser($fakeUser, 'foo', 'test');
    }

    public function testBindUserTypes()
    {
        /** @var BindingService|M\Mock $service */
        $service = M::mock(BindingService::class)->makePartial();

        $service->shouldReceive('bindUserId')->times(3)->with(1337, 'foo', 'test');
        $service->bindUserEntity( M::mock(UserEntity::class, ['getUserID' => 1337]), 'foo', 'test');
        $service->bindUserInfo( M::mock(UserInfo::class, ['getUserID' => 1337]), 'foo', 'test');
        $service->bindUser( M::mock(User::class, ['getUserID' => 1337]), 'foo', 'test');
    }

    private function createFakeConnection()
    {
        $connection = M::mock(Connection::class);
        $connection->shouldReceive('createQueryBuilder')->andReturnUsing(function() use ($connection) {
            /** @var QueryBuilder $qb */
            $qb = M::mock(QueryBuilder::class)->makePartial();
            $qb->__construct($connection);

            return $qb;
        });
        $connection->shouldReceive('getExpressionBuilder')->andReturn(M::mock(ExpressionBuilder::class)->makePartial());
        $connection->shouldReceive('transactional')->andReturnUsing(function($fn) use ($connection) {
            $fn($connection);
        });

        return $connection;
    }
}
