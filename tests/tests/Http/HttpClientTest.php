<?php

namespace Concrete\Tests\Http;

use Concrete\Core\Http\Client\Client as HttpClient;
use Concrete\Core\Http\Client\Factory as HttpClientFactory;
use Concrete\Core\Support\Facade\Application;
use Exception;
use PHPUnit_Framework_TestCase;
use Zend\Http\Client\Adapter\Curl as CurlHttpAdapter;
use Zend\Http\Client\Adapter\Exception\InitializationException as ZendInitializationException;
use Zend\Http\Client\Adapter\Proxy as SocketHttpAdapter;

class HttpClientTest extends PHPUnit_Framework_TestCase
{
    const SKIP_VALID_CERTS = '**skip**';

    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    /**
     * @var string
     */
    private static $testRemoteURI;

    public static function setUpBeforeClass()
    {
        self::$app = Application::getFacadeApplication();
        self::$testRemoteURI = getenv('CONCRETE5TESTS_TEST_REMOTE_URI') ?: 'https://www.concrete5.org/';
    }

    public function testAdapterKind()
    {
        $factory = self::$app->make(HttpClientFactory::class);

        if (function_exists('curl_init')) {
            $defaultAdapter = CurlHttpAdapter::class;
        } else {
            $defaultAdapter = SocketHttpAdapter::class;
        }

        $client = self::$app->make(HttpClient::class);
        $this->assertInstanceOf(HttpClient::class, $client);
        $this->assertInstanceOf($defaultAdapter, $client->getAdapter());

        $client = self::$app->make('http/client');
        $this->assertInstanceOf(HttpClient::class, $client);
        $this->assertInstanceOf($defaultAdapter, $client->getAdapter());

        if (function_exists('curl_init')) {
            $client = self::$app->make('http/client/curl');
            $this->assertInstanceOf(HttpClient::class, $client);
            $this->assertInstanceOf(CurlHttpAdapter::class, $client->getAdapter());
        } else {
            $error = null;
            try {
                $client = self::$app->make('http/client/curl');
                $adapter = $client->getAdapter();
            } catch (Exception $x) {
                $error = $x;
            }
            $this->assertInstanceOf(ZendInitializationException::class, $error);
        }

        $client = self::$app->make('http/client/socket');
        $this->assertInstanceOf(HttpClient::class, $client);
        $this->assertInstanceOf(SocketHttpAdapter::class, $client->getAdapter());

        if (function_exists('curl_init')) {
            $client = $factory->createFromOptions([], CurlHttpAdapter::class);
            $this->assertInstanceOf(CurlHttpAdapter::class, $client->getAdapter());
        }
        $client = $factory->createFromOptions([], SocketHttpAdapter::class);
        $this->assertInstanceOf(SocketHttpAdapter::class, $client->getAdapter());
    }

    public function adapterListProvider()
    {
        return [
            [CurlHttpAdapter::class],
            [SocketHttpAdapter::class],
        ];
    }

    public function sslOptionsProvider()
    {
        $result = [];
        foreach ($this->adapterListProvider() as $al) {
            $adapterClass = $al[0];
            $result[] = [
                $adapterClass,
                '/this/file/does/not/exist',
                '/this/directory/does/not/exist',
                false,
            ];
            $result[] = [
                $adapterClass,
                str_replace(DIRECTORY_SEPARATOR, '/', __FILE__),
                str_replace(DIRECTORY_SEPARATOR, '/', __DIR__),
                false,
            ];
            $certsFolder = self::SKIP_VALID_CERTS;
            $checkFolders = [
                '/etc/ssl/certs',
                '/etc/pki/tls/certs',
                '/etc/pki/CA/certs',
                '/system/etc/security/cacerts',
            ];
            foreach ($checkFolders as $checkFolder) {
                if (@is_dir($checkFolder)) {
                    $files = glob("$checkFolder/*.pem");
                    if (empty($files)) {
                        $files = glob("$checkFolder/*.crt");
                    }
                    if (!empty($files)) {
                        $certsFolder = $checkFolder;
                        break;
                    }
                }
            }
            $result[] = [
                $adapterClass,
                'invalid',
                $certsFolder,
                true,
            ];
        }

        return $result;
    }

    /**
     * @dataProvider sslOptionsProvider
     *
     * @param mixed $adapterClass
     * @param mixed $caFile
     * @param mixed $caPath
     * @param mixed $shouldBeOK
     *
     * @group online
     */
    public function testSSLOptions($adapterClass, $caFile, $caPath, $shouldBeOK)
    {
        $this->checkValidAdapter($adapterClass, true);

        $factory = self::$app->make(HttpClientFactory::class);

        $client = $factory->createFromOptions([
            'ssltransport' => 'tls',
            'sslverifypeer' => false,
            'sslcafile' => $caFile,
            'sslcapath' => $caPath,
        ], $adapterClass);
        $error = null;
        try {
            $client->setMethod('HEAD')->setUri(self::$testRemoteURI)->send();
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertTrue($error === null, 'sslverifypeer turned off should always succeed (error: ' . ($error ? $error->getMessage() : '') . ')');

        if ($shouldBeOK && $caPath === self::SKIP_VALID_CERTS) {
            $this->markTestSkipped('Unable to find a local folder containing CA certificates');
        }
        $client = $factory->createFromOptions([
            'ssltransport' => 'tls',
            'sslverifypeer' => true,
            'sslcafile' => $shouldBeOK ? null : $caFile,
            'sslcapath' => $caPath,
        ], $adapterClass);
        $error = null;
        try {
            $client->setMethod('HEAD')->setUri(self::$testRemoteURI)->send();
        } catch (Exception $x) {
            $error = $x;
        }
        if ($shouldBeOK) {
            $this->assertTrue($error === null, 'sslverifypeer turned on with correct SSL parameters should succeed (error: ' . ($error ? $error->getMessage() : '') . ')');
        } else {
            $this->assertTrue($error !== null, 'sslverifypeer turned on with incorrect SSL parameters should fail');
        }
    }

    /**
     * @dataProvider adapterListProvider
     *
     * @param mixed $adapterClass
     */
    public function testNormalizingOptions($adapterClass)
    {
        $this->checkValidAdapter($adapterClass, true);

        $factory = self::$app->make(HttpClientFactory::class);

        $client = $factory->createFromOptions([
            'SSL-VERIFYPEER' => true,
            'proxyHost' => 'host',
            'proxy-port' => '12345',
            'PROXY USER' => 'me',
            'PROXY.PASS' => null,
            '   S S L C A F I L E ' => null,
            'sslcapath' => null,
            'connect Timeout' => 5,
            'keepalive' => false,
            'maxredirects' => 5,
            'rfc3986strict' => '1',
            'sslcert' => null,
            'sslpassphrase' => 'unused',
            'storeresponse' => 0,
            'strictredirects' => 'yes',
            'user agent' => 'Test User Agent',
            'encodecookies' => '',
            'httpversion' => '1.1',
            'ssltransport' => 'tls',
            'sslallowselfsigned' => 1,
            'persistent' => '',
            'unknownKey' => 'Unknown value',
        ], $adapterClass);
        $adapterOptions = $client->getAdapter()->getConfig();
        $expectedOptions = [
            'sslverifypeer' => true,
            'proxyhost' => 'host',
            'proxyport' => 12345,
            'proxyuser' => 'me',
            'proxypass' => '',
            'sslcafile' => null,
            'sslcapath' => null,
            'connecttimeout' => 5,
            'timeout' => 60,
            'keepalive' => false,
            'maxredirects' => 5,
            'rfc3986strict' => true,
            'sslcert' => null,
            'storeresponse' => false,
            'streamtmpdir' => self::$app->make('helper/file')->getTemporaryDirectory(),
            'strictredirects' => true,
            'useragent' => 'Test User Agent',
            'encodecookies' => true,
            'httpversion' => '1.1',
            'ssltransport' => 'tls',
            'sslallowselfsigned' => true,
            'persistent' => false,
        ];
        if ($adapterClass === CurlHttpAdapter::class) {
            $expectedOptions['curloptions'] = [
                CURLOPT_SSL_VERIFYPEER => $expectedOptions['sslverifypeer'],
                CURLOPT_PROXY => $expectedOptions['proxyhost'],
                CURLOPT_PROXYPORT => $expectedOptions['proxyport'],
                CURLOPT_PROXYUSERPWD => $expectedOptions['proxyuser'] . ':' . $expectedOptions['proxypass'],
            ];
            unset($expectedOptions['sslverifypeer']);
            unset($expectedOptions['proxyhost']);
            unset($expectedOptions['proxyport']);
            unset($expectedOptions['proxyuser']);
            unset($expectedOptions['proxypass']);
            ksort($expectedOptions['curloptions']);
        } elseif ($adapterClass === SocketHttpAdapter::class) {
            foreach (array_keys($expectedOptions) as $key) {
                if (preg_match('/^(proxy)(.+)$/', $key, $matches)) {
                    $expectedOptions[$matches[1] . '_' . $matches[2]] = $expectedOptions[$key];
                    unset($expectedOptions[$key]);
                }
            }
        }
        if (isset($adapterOptions['curloptions'])) {
            ksort($adapterOptions['curloptions']);
        }
        foreach ($expectedOptions as $key => $value) {
            $this->assertArrayHasKey($key, $adapterOptions);
            $this->assertSame($value, $adapterOptions[$key], 'Checking key ' . $key);
        }
    }

    private function checkValidAdapter($adapterClass, $forSSL)
    {
        if ($adapterClass === CurlHttpAdapter::class && !function_exists('curl_init')) {
            $this->markTestSkipped('Skipped tests on cURL HTTP Client Adapter since the PHP cURL extension is not enabled');
        }
        if ($adapterClass === SocketHttpAdapter::class && $forSSL && !function_exists('stream_socket_enable_crypto')) {
            $this->markTestSkipped('stream_socket_enable_crypto is not implemented (is this HHVM?)');
        }
    }
}
