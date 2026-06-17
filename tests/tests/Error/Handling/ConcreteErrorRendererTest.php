<?php

namespace Concrete\Tests\Error\Handling;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\Handling\ErrorRenderer\ConcreteErrorRenderer;
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
}
