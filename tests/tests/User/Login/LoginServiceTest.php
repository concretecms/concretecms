<?php

namespace User\Login;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Logging\Entry\User\LoginAttempt;
use Concrete\Core\Permission\IPService;
use Concrete\Core\User\Exception\FailedLoginThresholdExceededException;
use Concrete\Core\User\Exception\InvalidCredentialsException;
use Concrete\Core\User\Exception\NotActiveException;
use Concrete\Core\User\Exception\NotValidatedException;
use Concrete\Core\User\Exception\SessionExpiredException;
use Concrete\Core\User\Exception\UserDeactivatedException;
use Concrete\Core\User\Login\LoginAttemptService;
use Concrete\Core\User\Login\LoginService;
use Concrete\Tests\User\Login\MockUser;
use Doctrine\ORM\EntityManagerInterface;
use IPLib\Address\AddressInterface;
use IPLib\Address\IPv4;
use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

class LoginServiceTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    protected $config;

    public function testLoggingLoginAttempt()
    {
        // Setup mocks
        $app = M::mock(Application::class);
        $config = M::mock(Repository::class);
        $attemptService = M::mock(LoginAttemptService::class);
        $ipService = M::mock(IPService::class);
        $mockIpObject = M::mock(IPv4::class);
        $entityManager = M::mock(EntityManagerInterface::class);
        $entityManager->shouldIgnoreMissing($entityManager);

        $request = M::mock(Request::class);
        $request->shouldReceive('getPath')->andReturn('/foo/bar');

        $logger = M::mock(LoggerInterface::class);

        $attempt = M::mock(LoginAttempt::class);

        $mockIpObject->shouldReceive('__toString')->andReturn('127.0.0.1');

        $ipService->shouldReceive('getRequestIPAddress')->andReturn($mockIpObject);
        $service = new LoginService($config, $attemptService, $ipService, $entityManager, $request);
        $service->setLogger($logger);
        $service->setApplication($app);

        // Fake mocked methods
        $app->shouldReceive('make')
            ->withArgs([
                LoginAttempt::class,
                [
                    'foo',
                    '/foo/bar',
                    ['baz'],
                    ['Something went wrong']
                ]
            ])
            ->andReturn($attempt);

        $entityManager->shouldReceive('execute')->andReturn([['gName' => 'baz', 'uID' => 5]]);

        $attempt->shouldReceive('getMessage')->andReturn('Foo did login!');
        $attempt->shouldReceive('getContext')->andReturn([1,2,3]);

        $mockContext = [
            1,
            2,
            3,
            'ip_address' => '127.0.0.1'
        ];

        // Set the real expectation we care about
        $logger->shouldReceive('info')->once()->withArgs([
            'Foo did login!',
            $mockContext,
        ]);

        // Run the test
        $service->logLoginAttempt('foo', ['Something went wrong']);
    }

    public function testLogin()
    {
        $config = M::mock(Repository::class);
        $attemptService = M::mock(LoginAttemptService::class);
        $ipService = M::mock(IPService::class);

        // Set the error
        MockUser::$error = null;

        $service = new LoginService($config, $attemptService, $ipService);
        $service->setUserClass(MockUser::class);
        $result = $service->login('foo', 'bar');

        // Make sure our username and password is piped into our given classes constructor
        $this->assertEquals(['foo', 'bar'], $result->input);

        // Make sure the loginservice verified the user is not errored
        $this->assertTrue($result->isErrorCalled);
    }

    /**
     * @dataProvider loginErrors
     */
    public function testLoginWithError($errorNum, $exceptionClass, $emailRegistration = false, $messageFormat = null)
    {
        $config = M::mock(Repository::class);
        $config->shouldReceive('get')->withArgs(['concrete.user.deactivation.message'])->andReturn('Itz brok, halp');
        $config->shouldReceive('get')->withArgs(['concrete.user.registration.email_registration'])->andReturn($emailRegistration);

        $attemptService = M::mock(LoginAttemptService::class);
        $ipService = M::mock(IPService::class);

        // Set the error
        MockUser::$error = $errorNum;

        $service = new LoginService($config, $attemptService, $ipService);
        $service->setUserClass(MockUser::class);

        $e = null;
        try {
            $service->login('foo', 'bar');
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e, 'No exception thrown when user presents error');

        // Make sure the expected exception class is provided
        $this->assertSame($exceptionClass, get_class($e), 'Invalid exception thrown on login.');

        if ($messageFormat) {
            $this->assertStringMatchesFormat($messageFormat, $e->getMessage());
        }
    }

    public function loginErrors()
    {
        return [
            [USER_INACTIVE, NotActiveException::class],
            [USER_NON_VALIDATED, NotValidatedException::class],
            [USER_SESSION_EXPIRED, SessionExpiredException::class],
            [1337, \RuntimeException::class],
            [USER_INVALID, InvalidCredentialsException::class, false, 'Invalid username or password.'],
            // Run again with email login enabled
            [USER_INVALID, InvalidCredentialsException::class, true, 'Invalid email address or password.'],
        ];
    }

    public function testLoginByUserID()
    {
        $config = M::mock(Repository::class);
        $attemptService = M::mock(LoginAttemptService::class);
        $ipService = M::mock(IPService::class);

        $userMock = M::mock('alias:MockUserLoginByID');
        $userMock->shouldReceive('getByUserID')->withArgs([1337, true])->once();
        $userMock->shouldReceive('getByUserID')->withArgs([1338, true])->once();

        $service = new LoginService($config, $attemptService, $ipService);
        $service->setUserClass(\MockUserLoginByID::class);

        $service->loginByUserID(1337);
        $service->loginByUserID(1338);
    }

    public function testFailLoginUserDeactivated()
    {
        $config = M::mock(Repository::class);
        $config->shouldReceive('get')->withArgs(['concrete.user.deactivation.message'])->andReturn('Expected Message');

        $attemptService = M::mock(LoginAttemptService::class);
        $ipService = M::mock(IPService::class);

        $service = new LoginService($config, $attemptService, $ipService);
        $service->setUserClass(\MockUserLoginByID::class);

        $ipService->shouldReceive('logFailedLogin')->times(3);
        $ipService->shouldReceive('failedLoginsThresholdReached')->times(3)->andReturn(false);

        $attemptService->shouldReceive('trackAttempt')->once()->withArgs(['foo', 'bar']);
        $attemptService->shouldReceive('trackAttempt')->once()->withArgs(['foo', 'baz']);
        $attemptService->shouldReceive('trackAttempt')->once()->withArgs(['foobar', 'baz']);
        $attemptService->shouldReceive('remainingAttempts')->times(3)->andReturnValues([2, 1, 0]);
        $attemptService->shouldReceive('deactivate')->once();

        $service->failLogin('foo', 'bar');
        $service->failLogin('foo', 'baz');

        $e = null;
        try {
            $service->failLogin('foobar', 'baz');
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e, 'Failed to hit deactivation threshold');
        $this->assertInstanceOf(UserDeactivatedException::class, $e);
    }

    public function testFailLoginIpLimitReached()
    {
        $config = M::mock(Repository::class);
        $attemptService = M::mock(LoginAttemptService::class);
        $ipService = M::mock(IPService::class);

        $service = new LoginService($config, $attemptService, $ipService);
        $service->setUserClass(\MockUserLoginByID::class);

        $ipService->shouldReceive('logFailedLogin')->twice();
        $ipService->shouldReceive('failedLoginsThresholdReached')->twice()->andReturnValues([false, true]);
        $ipService->shouldReceive('addToBlacklistForThresholdReached')->once();
        $ipService->shouldReceive('getErrorMessage')->once()->andReturn('Error Message');

        $attemptService->shouldReceive('trackAttempt');
        $attemptService->shouldReceive('remainingAttempts')->andReturn(1);

        $service->failLogin('foo', 'bar');

        $e = null;
        try {
            $service->failLogin('foo', 'baz');
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e, 'Failed to hit deactivation threshold');
        $this->assertInstanceOf(FailedLoginThresholdExceededException::class, $e);
    }
}
