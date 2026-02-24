<?php

namespace Concrete\Tests\Mail\Transport;

use Concrete\Core\Application\Application;
use Concrete\Core\Mail\Transport\Factory;
use Concrete\Tests\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class FactoryTest extends TestCase
{
    /** @var Application */
    private $app;
    /** @var LoggerInterface */
    private $logger;
    /** @var EventDispatcher */
    private $dispatcher;
    /** @var Factory */
    private $factory;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
        $this->logger = new TestLogger();
        $this->app = \Mockery::mock(Application::class);
        $this->app->shouldReceive('make->getEventDispatcher')->andReturn($this->dispatcher);
        $this->factory = new Factory();
        $this->factory->setApplication($this->app);
        $this->factory->setLogger($this->logger);
    }

    /**
     * @dataProvider provideSendmail
     */
    public function testCreateSendmailTransportFromArray(?string $method, ?string $parameters): void
    {
        $transport = $this->factory->createTransportFromArray([
            'method' => $method,
            'methods' => [
                'php_mail' => [
                    'parameters' => $parameters,
                ]
            ]
        ]);

        $this->assertInstanceOf(SendmailTransport::class, $transport);
    }

    public function testCreateSendmailFailsOnInvalidParameters(): void
    {
        $this->expectExceptionMessage("Unsupported sendmail command flags");
        $this->factory->createTransportFromArray([
            'method' => 'PHP_MAIL',
            'methods' => [
                'php_mail' => [
                    'parameters' => 'Foo',
                ]
            ]
        ]);
    }

    public static function provideSendmail(): iterable
    {
        $methods = ['PHP_MAIL', 'foo', null];
        $params = ['sendmail -bs', 'sendmail -t', '', null];
        return collect($methods)->crossJoin($params);
    }

    /**
     * @dataProvider provideSmtp
     */
    public function testCreateSmtpTransportFromArray(?string $encryption): void
    {
        $server = uuid_create();
        $transport = $this->factory->createTransportFromArray([
            'method' => 'smtp',
            'methods' => [
                'smtp' => [
                    'server' => $server,
                    'port' => 999,
                    'encryption' => $encryption,
                    'username' => 'user',
                    'password' => 'pass',
                    'helo_domain' => 'foo',
                    'messages_per_connection' => 10,
                ]
            ]
        ]);

        $this->assertInstanceOf(EsmtpTransport::class, $transport);
        $s = $encryption ? 's' : '';
        $this->assertEquals("smtp{$s}://{$server}:999", (string) $transport);
        $this->assertEquals('foo', $transport->getLocalDomain());
        $this->assertEquals('user', $transport->getUsername());
        $this->assertEquals('pass', $transport->getPassword());
    }

    public static function provideSmtp(): iterable
    {
        return collect(['TLS', 'SSL', 'tls', '', null])->crossJoin();
    }
}