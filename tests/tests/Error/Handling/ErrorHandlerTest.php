<?php

namespace Concrete\Tests\Error\Handling;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\Handling\ErrorHandler;
use Concrete\Tests\TestCase;
use Mockery;
use Psr\Log\NullLogger;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;

class ErrorHandlerTest extends TestCase
{
    private $originalServer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
        parent::tearDown();
    }

    public function testEmergencyHtmlFallbackUsesGuestSafeGenericMessage(): void
    {
        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')
            ->with('concrete.error.handling')
            ->andReturn([]);
        $config->shouldReceive('get')
            ->once()
            ->with('concrete.error.display.guests', 'generic')
            ->andReturn('generic');

        $handler = new TestableErrorHandler($config, new ThrowingErrorRenderer());

        ob_start();
        $handler->renderForTest(new \RuntimeException('This should stay hidden.'));
        $output = (string) ob_get_clean();

        $this->assertStringContainsString('An unexpected error occurred.', $output);
        $this->assertStringContainsString('An error occurred while processing this request.', $output);
        $this->assertStringNotContainsString('This should stay hidden.', $output);
    }

    public function testEmergencyJsonFallbackCanStillShowGuestMessageMode(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')
            ->with('concrete.error.handling')
            ->andReturn([]);
        $config->shouldReceive('get')
            ->once()
            ->with('concrete.error.display.guests', 'generic')
            ->andReturn('message');

        $handler = new TestableErrorHandler($config, new ThrowingErrorRenderer());

        ob_start();
        $handler->renderForTest(new \RuntimeException('Renderer exploded.'));
        $output = (string) ob_get_clean();
        $payload = json_decode($output, true);

        $this->assertSame(['error' => true, 'errors' => ['Renderer exploded.']], $payload);
        $this->assertArrayNotHasKey('trace', $payload);
    }
}

class TestableErrorHandler extends ErrorHandler
{
    /**
     * @var \Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface
     */
    private $renderer;

    public function __construct(Repository $config, ErrorRendererInterface $renderer)
    {
        $this->renderer = $renderer;
        parent::__construct(new NullLogger(), $config);
    }

    public function renderForTest(\Throwable $exception): void
    {
        $this->renderConcreteException($exception);
    }

    protected function createRenderer(): ErrorRendererInterface
    {
        return $this->renderer;
    }
}

class ThrowingErrorRenderer implements ErrorRendererInterface
{
    public function render(\Throwable $exception): \Symfony\Component\ErrorHandler\Exception\FlattenException
    {
        throw new \RuntimeException('Renderer failure.');
    }
}
