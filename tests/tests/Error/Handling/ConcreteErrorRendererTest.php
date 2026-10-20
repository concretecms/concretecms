<?php

namespace Concrete\Tests\Error\Handling;

use Concrete\Core\Api\Exception\InvalidLimitQueryParameterValueException;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\Handling\ErrorRenderer\ConcreteErrorRenderer;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Error\UserMessageHttpException;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Tests\TestCase;
use Mockery;

class ConcreteErrorRendererTest extends TestCase
{
    public function testPermissionCheckFailuresFallBackToGuestSafeJsonOutput(): void
    {
        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')
            ->once()
            ->with('concrete.error.display.guests', 'generic')
            ->andReturn('generic');

        $checker = Mockery::mock(Checker::class);
        $checker->shouldReceive('canViewDebugErrorInformation')
            ->once()
            ->andThrow(new \RuntimeException('Permission storage is unavailable.'));

        $renderer = new ConcreteErrorRenderer(
            $config,
            $checker,
            Request::create('/', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json'])
        );

        $flattenException = $renderer->render(new \RuntimeException('Database is down.'));
        $payload = json_decode($flattenException->getAsString(), true);

        $this->assertSame(['error' => true, 'errors' => ['An error occurred while processing this request.']], $payload);
    }

    public function testPrivilegedUsersStillOptIntoDebugJsonOutput(): void
    {
        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')
            ->once()
            ->with('concrete.error.display.guests', 'generic')
            ->andReturn('generic');
        $config->shouldReceive('get')
            ->once()
            ->with('concrete.error.display.privileged', 'generic')
            ->andReturn('debug');

        $checker = Mockery::mock(Checker::class);
        $checker->shouldReceive('canViewDebugErrorInformation')
            ->once()
            ->andReturnTrue();

        $renderer = new ConcreteErrorRenderer(
            $config,
            $checker,
            Request::create('/', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json'])
        );

        $flattenException = $renderer->render(new \RuntimeException('Detailed failure.'));
        $payload = json_decode($flattenException->getAsString(), true);

        $this->assertSame('Detailed failure.', $payload['errors'][0] ?? null);
        $this->assertArrayHasKey('trace', $payload);
    }

    /**
     * @return array<int,array{\Concrete\Core\Error\UserMessageException,string,int}>
     */
    public static function provideExceptionsAndTheirStatus(): array
    {
        return [
            [new UserMessageHttpException('Invalid file version.', 400), 'application/json', 400],
            [new UserMessageHttpException('Access Denied.', 403), 'application/json', 403],
            [new UserMessageHttpException('Access Denied.', 403), 'text/html', 403],
            [new InvalidLimitQueryParameterValueException(), 'application/json', 400],
            // an error nobody saw coming is a failure of the server, whatever code it carries
            [new UserMessageException('Invalid file version.', 400), 'application/json', 500],
        ];
    }

    /**
     * @dataProvider provideExceptionsAndTheirStatus
     */
    public function testTheAnswerCarriesTheStatusTheExceptionNames(UserMessageException $exception, string $accept, int $expectedStatus): void
    {
        $renderer = new ConcreteErrorRenderer(
            Mockery::mock(Repository::class),
            Mockery::mock(Checker::class),
            Request::create('/', 'GET', [], [], [], ['HTTP_ACCEPT' => $accept])
        );

        $this->assertSame($expectedStatus, $renderer->render($exception)->getStatusCode());
    }
}
