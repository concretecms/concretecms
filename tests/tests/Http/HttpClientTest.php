<?php

declare(strict_types=1);

namespace Concrete\Tests\Http;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Client\Client as HttpClient;
use Concrete\Core\Http\Client\Factory as HttpClientFactory;
use Concrete\TestHelpers\Http\FakeHttpsServer;
use Concrete\Tests\TestCase;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\Promise\Create as PromiseCreate;
use GuzzleHttp\Psr7\Response;
use Mockery as M;
use Psr\Http\Message\RequestInterface;
use Psr\Log\NullLogger;

defined('C5_EXECUTE') or die('Access Denied.');

class HttpClientTest extends TestCase
{
    /**
     * The timeouts of the requests sent to the fake HTTPS server.
     *
     * @var array
     */
    private const TIMEOUT_OPTIONS = ['connect_timeout' => 5, 'timeout' => 10];

    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    /**
     * The options that the last request passed to the handler.
     *
     * @var array|null
     */
    private $handledOptions;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$app = app();
    }

    public static function handlerListProvider(): array
    {
        return [
            [CurlHandler::class],
            [StreamHandler::class],
        ];
    }

    public function testCreateFromOptionsAppliesTheOptionsToTheRequests(): void
    {
        $client = $this->createClientWithCapturingHandler([
            'timeout' => 12,
            'connect_timeout' => 3,
            'verify' => false,
        ]);

        $client->request('GET', 'http://www.example.com/');

        static::assertSame(12, $this->handledOptions['timeout']);
        static::assertSame(3, $this->handledOptions['connect_timeout']);
        static::assertFalse($this->handledOptions['verify']);
    }

    public function testCreateFromOptionsWithAHandlerClassName(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);

        $client = $factory->createFromOptions([], CurlHandler::class);

        static::assertInstanceOf(HttpClient::class, $client);
        static::assertInstanceOf(CurlHandler::class, $client->getConfig('handler'));
    }

    public function testCreateFromOptionsWithAHandlerInstance(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        $handler = new CurlHandler();

        $client = $factory->createFromOptions([], $handler);

        static::assertSame($handler, $client->getConfig('handler'));
    }

    public function testCreateFromOptionsWithoutALogger(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);

        $client = $factory->createFromOptions([]);

        static::assertNull($client->getLogger());
    }

    public function testCreateFromOptionsWithALoggerInstance(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        $logger = new NullLogger();

        $client = $factory->createFromOptions(['logger' => $logger]);

        static::assertSame($logger, $client->getLogger());
    }

    public function testCreateFromOptionsWithALoggerClassName(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);

        $client = $factory->createFromOptions(['logger' => NullLogger::class]);

        static::assertInstanceOf(NullLogger::class, $client->getLogger());
    }

    public function testCreateFromConfigReadsTheHttpClientOptions(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        $config = $this->buildConfig(['timeout' => 42, 'verify' => false]);

        $client = $factory->createFromConfig($config);

        static::assertSame(42, $client->getConfig('timeout'));
        static::assertFalse($client->getConfig('verify'));
    }

    public function testCreateFromConfigWithInvalidHttpClientOptions(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        // The app.http_client configuration key is not an array: it must be ignored.
        $config = $this->buildConfig(null);

        $client = $factory->createFromConfig($config);

        static::assertInstanceOf(HttpClient::class, $client);
        static::assertNull($client->getConfig('proxy'));
    }

    public function testCreateFromConfigWithoutAProxy(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        // A proxy port/user/password without a proxy host must not build any proxy.
        $config = $this->buildConfig([], ['port' => 8080, 'user' => 'me', 'password' => 'secret']);

        $options = $factory->getDefaultOptions($config);

        static::assertArrayNotHasKey('proxy', $options);
    }

    /**
     * @dataProvider proxyProvider
     */
    public function testCreateFromConfigBuildsTheProxyUrl(array $proxy, string $expectedProxyUrl): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        $config = $this->buildConfig([], $proxy);

        $options = $factory->getDefaultOptions($config);

        static::assertSame($expectedProxyUrl, $options['proxy']);
    }

    public static function proxyProvider(): array
    {
        return [
            [
                ['host' => 'http://proxy.example.com'],
                'http://proxy.example.com/',
            ],
            [
                ['host' => 'http://proxy.example.com', 'port' => 8080],
                'http://proxy.example.com:8080/',
            ],
            [
                ['host' => 'http://proxy.example.com', 'port' => 8080, 'user' => 'me', 'password' => 'secret'],
                'http://me:secret@proxy.example.com:8080/',
            ],
        ];
    }

    public function testTheProxyIsUsedByTheRequests(): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        $config = $this->buildConfig([], ['host' => 'http://proxy.example.com', 'port' => 8080]);
        $options = $factory->getDefaultOptions($config);
        $client = $factory->createFromOptions($options, $this->buildCapturingHandler());

        $client->request('GET', 'http://www.example.com/');

        static::assertSame('http://proxy.example.com:8080/', $this->handledOptions['proxy']);
    }

    /**
     * When the "verify" option is not specified, the peer verification must be turned on: requests to the fake HTTPS
     * server (which uses a self-signed certificate) must fail.
     *
     * @dataProvider handlerListProvider
     */
    public function testSSLVerificationIsEnabledByDefault(string $handlerClass): void
    {
        $client = $this->createClientForTheFakeServer([], $handlerClass);

        $error = $this->headRequestError($client);

        static::assertNotNull($error, 'requests without an explicit "verify" option should verify the peer, and fail');
    }

    /**
     * The "verify" option shipped with the core configuration must turn on the peer verification.
     */
    public function testSSLVerificationIsEnabledByTheCoreConfiguration(): void
    {
        $config = self::$app->make(Repository::class);

        static::assertTrue($config->get('app.http_client.verify'));
    }

    /**
     * @dataProvider handlerListProvider
     */
    public function testSSLVerificationFromTheConfiguration(string $handlerClass): void
    {
        $factory = self::$app->make(HttpClientFactory::class);
        $server = self::getFakeServer();
        $this->checkValidHandler($handlerClass);

        // The configuration doesn't trust the certification authority of the fake server: the request must fail.
        $client = $factory->createFromConfig($this->buildConfig(self::TIMEOUT_OPTIONS + ['verify' => true]), $handlerClass);
        static::assertNotNull($this->headRequestError($client), 'a configuration with verify turned on should fail');

        // The configuration trusts the certification authority of the fake server: the request must succeed.
        $client = $factory->createFromConfig($this->buildConfig(self::TIMEOUT_OPTIONS + ['verify' => FakeHttpsServer::getCACertificateFile()]), $handlerClass);
        static::assertSame(200, $client->get($server->getUri('/'))->getStatusCode());
    }

    /**
     * The fake HTTPS server uses a self-signed certificate: since it's not signed by a well known certification
     * authority, the requests must fail when the peer verification is explicitly turned on.
     *
     * @dataProvider handlerListProvider
     */
    public function testSSLVerificationAgainstTheDefaultCABundle(string $handlerClass): void
    {
        $client = $this->createClientForTheFakeServer(['verify' => true], $handlerClass);

        $error = $this->headRequestError($client);

        static::assertNotNull($error, 'verify turned on with the default CA bundle should fail');
    }

    /**
     * Clients explicitly accepting unsafe servers must be able to talk with the fake HTTPS server.
     *
     * @dataProvider handlerListProvider
     */
    public function testSSLVerificationTurnedOff(string $handlerClass): void
    {
        $client = $this->createClientForTheFakeServer(['verify' => false], $handlerClass);

        $error = $this->headRequestError($client);

        static::assertNull($error, 'verify turned off should always succeed (error: ' . ($error === null ? '' : $error->getMessage()) . ')');
    }

    /**
     * Clients trusting the certification authority that signed the certificate of the fake HTTPS server must be able
     * to talk with it, without turning off the peer verification.
     *
     * @dataProvider handlerListProvider
     */
    public function testSSLVerificationAgainstTheFakeCABundle(string $handlerClass): void
    {
        $client = $this->createClientForTheFakeServer(['verify' => FakeHttpsServer::getCACertificateFile()], $handlerClass);

        $response = $client->get(self::getFakeServer()->getUri('/'));

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('fake HTTPS server', (string) $response->getBody());
    }

    /**
     * @dataProvider handlerListProvider
     */
    public function testSSLVerificationAgainstAnInvalidCABundle(string $handlerClass): void
    {
        $notACaBundle = str_replace(DIRECTORY_SEPARATOR, '/', __FILE__);
        $client = $this->createClientForTheFakeServer(['verify' => $notACaBundle], $handlerClass);

        $error = $this->headRequestError($client);

        static::assertNotNull($error, 'verify turned on with an invalid CA bundle should fail');
    }

    /**
     * Get the fake HTTPS server started by the PHPUnit bootstrap, skipping the test if it's not available.
     */
    private static function getFakeServer(): FakeHttpsServer
    {
        $server = FakeHttpsServer::getInstance();
        if ($server === null) {
            static::markTestSkipped('The fake HTTPS server is not available: ' . FakeHttpsServer::getStartupError());
        }
        if (!$server->isRunning()) {
            static::fail('The fake HTTPS server is no longer running');
        }

        return $server;
    }

    /**
     * Create a client to be used to perform requests to the fake HTTPS server.
     */
    private function createClientForTheFakeServer(array $options, string $handlerClass): HttpClient
    {
        $this->checkValidHandler($handlerClass);
        self::getFakeServer();
        $factory = self::$app->make(HttpClientFactory::class);

        return $factory->createFromOptions($options + self::TIMEOUT_OPTIONS, $handlerClass);
    }

    /**
     * Perform a HEAD request against the fake HTTPS server, returning the raised error (if any).
     */
    private function headRequestError(HttpClient $client): ?\Throwable
    {
        try {
            $client->head(self::getFakeServer()->getUri('/'));
        } catch (\Throwable $x) {
            return $x;
        }

        return null;
    }

    /**
     * Skip the test if the given handler can't be used in the current environment.
     */
    private function checkValidHandler(string $handlerClass): void
    {
        if ($handlerClass === CurlHandler::class && !function_exists('curl_init')) {
            static::markTestSkipped('Skipped tests on the cURL handler since the PHP cURL extension is not enabled');
        }
        if ($handlerClass === StreamHandler::class && !filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            static::markTestSkipped('Skipped tests on the stream handler since allow_url_fopen is disabled');
        }
    }

    /**
     * Build a configuration repository providing the given app.http_client and concrete.proxy values.
     *
     * @param mixed $httpClientOptions the value of the app.http_client configuration key
     * @param array $proxy the host/port/user/password values of the concrete.proxy configuration keys
     */
    private function buildConfig($httpClientOptions, array $proxy = []): Repository
    {
        $values = [
            'app.http_client' => $httpClientOptions,
            'concrete.proxy.host' => $proxy['host'] ?? null,
            'concrete.proxy.port' => $proxy['port'] ?? null,
            'concrete.proxy.user' => $proxy['user'] ?? null,
            'concrete.proxy.password' => $proxy['password'] ?? null,
        ];
        $config = M::mock(Repository::class);
        foreach ($values as $key => $value) {
            $config->shouldReceive('get')->with($key)->andReturn($value);
        }

        return $config;
    }

    /**
     * Build a handler that stores the options it receives in $this->handledOptions.
     */
    private function buildCapturingHandler(): \Closure
    {
        $this->handledOptions = null;

        return function (RequestInterface $request, array $options) {
            $this->handledOptions = $options;

            return PromiseCreate::promiseFor(new Response(200));
        };
    }

    /**
     * Create a client whose requests are handled by the capturing handler.
     */
    private function createClientWithCapturingHandler(array $options): HttpClient
    {
        $factory = self::$app->make(HttpClientFactory::class);

        return $factory->createFromOptions($options, $this->buildCapturingHandler());
    }
}
