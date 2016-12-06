<?php
namespace Concrete\Tests\Core\Localization;

use Concrete\Core\Http\Client\Adapter\Curl;
use Concrete\Core\Http\Client\Adapter\Socket;
use Concrete\Core\Http\Client\Client;
use Concrete\Core\Http\Client\Factory;
use Concrete\Core\Support\Facade\Application;
use Exception;
use PHPUnit_Framework_TestCase;
use Zend\Http\Client\Adapter\Exception\InitializationException as ZendInitializationException;

class HttpClientTest extends PHPUnit_Framework_TestCase
{
    const SKIP_VALID_CERTS = '**skip**';

    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    public static function setUpBeforeClass()
    {
        self::$app = Application::getFacadeApplication();
    }

    public function testAdapterKind()
    {
        if (function_exists('curl_init')) {
            $defaultAdapter = Curl::class;
        } else {
            $defaultAdapter = Socket::class;
        }

        $client = self::$app->make(Client::class);
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf($defaultAdapter, $client->getAdapter());

        $client = self::$app->make('http/client');
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf($defaultAdapter, $client->getAdapter());

        if (function_exists('curl_init')) {
            $client = self::$app->make('http/client/curl');
            $this->assertInstanceOf(Client::class, $client);
            $this->assertInstanceOf(Curl::class, $client->getAdapter());
        } else {
            $error = null;
            try {
                self::$app->make('http/client/curl');
            } catch (Exception $x) {
                $error = $x;
            }
            $this->assertInstanceOf(ZendInitializationException::class, $error);
        }

        $client = self::$app->make('http/client/socket');
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(Socket::class, $client->getAdapter());

        if (function_exists('curl_init')) {
            $client = self::$app->make(Factory::class)->createFromOptions([], Curl::class);
            $this->assertInstanceOf(Curl::class, $client->getAdapter());
        }
        $client = self::$app->make(Factory::class)->createFromOptions([], Socket::class);
        $this->assertInstanceOf(Socket::class, $client->getAdapter());
    }

    public function adapterListProvider()
    {
        return [
            [Curl::class],
            [Socket::class],
        ];
    }

    private function checkValidAdapter($adapterClass, $forSSL)
    {
        if ($adapterClass === Curl::class && !function_exists('curl_init')) {
            $this->markTestSkipped('Skipped tests on cURL HTTP Client Adapter since the PHP cURL extension is not enabled');
        }
        if ($adapterClass === Socket::class && $forSSL && !function_exists('stream_socket_enable_crypto')) {
            $this->markTestSkipped('stream_socket_enable_crypto is not implemented (is this HHVM?)');
        }
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
                __FILE__,
                __DIR__,
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
     */
    public function testSSLOptions($adapterClass, $caFile, $caPath, $shouldBeOK)
    {
        $this->checkValidAdapter($adapterClass, true);

        $client = self::$app->make(Factory::class)->createFromOptions([
            'ssltransport' => 'tls',
            'sslverifypeer' => false,
            'sslcafile' => $caFile,
            'sslcapath' => $caPath,
        ], $adapterClass);
        $error = null;
        try {
            $client->setMethod('GET')->setUri('https://www.google.com')->send();
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertTrue($error === null, 'sslverifypeer turned off should always succeed');

        if ($shouldBeOK && $caPath == $certsFolder = self::SKIP_VALID_CERTS) {
            $this->markTestSkipped('Unable to find a local folder containing CA certificates');
        }
        $client = self::$app->make(Factory::class)->createFromOptions([
            'ssltransport' => 'tls',
            'sslverifypeer' => true,
            'sslcafile' => $shouldBeOK ? null : $caFile,
            'sslcapath' => $caPath,
        ], $adapterClass);
        $error = null;
        try {
            $client->setMethod('GET')->setUri('https://www.google.com')->send();
        } catch (Exception $x) {
            $error = $x;
        }
        if ($shouldBeOK) {
            $this->assertTrue($error === null, 'sslverifypeer turned on with correct SSL parameters should succeed');
        } else {
            $this->assertTrue($error !== null, 'sslverifypeer turned on with incorrect SSL parameters should fail');
        }
    }

    /**
     * @dataProvider adapterListProvider
     */
    public function testNormalizingOptions($adapterClass)
    {
        $this->checkValidAdapter($adapterClass, true);

        $client = self::$app->make(Factory::class)->createFromOptions([
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
            'sslpassphrase' => null,
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
            'executetimeout' => 60,
            'keepalive' => false,
            'maxredirects' => 5,
            'rfc3986strict' => true,
            'sslcert' => null,
            'sslpassphrase' => null,
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
        if ($adapterClass === Curl::class) {
            $expectedOptions['curloptions'] = [
                CURLOPT_SSL_VERIFYPEER => $expectedOptions['sslverifypeer'],
                CURLOPT_PROXY => $expectedOptions['proxyhost'],
                CURLOPT_PROXYPORT => $expectedOptions['proxyport'],
                CURLOPT_PROXYUSERPWD => $expectedOptions['proxyuser'].':'.$expectedOptions['proxypass'],
            ];
            unset($expectedOptions['sslverifypeer']);
            unset($expectedOptions['proxyhost']);
            unset($expectedOptions['proxyport']);
            unset($expectedOptions['proxyuser']);
            unset($expectedOptions['proxypass']);
            ksort($expectedOptions['curloptions']);
        } elseif ($adapterClass === Socket::class) {
            foreach (array_keys($expectedOptions) as $key) {
                if (preg_match('/^(proxy)(.+)$/', $key, $matches)) {
                    $expectedOptions[$matches[1].'_'.$matches[2]] = $expectedOptions[$key];
                    unset($expectedOptions[$key]);
                }
            }
        }
        if (isset($adapterOptions['curloptions'])) {
            ksort($adapterOptions['curloptions']);
        }
        foreach ($expectedOptions as $key => $value) {
            $this->assertArrayHasKey($key, $adapterOptions);
            $this->assertSame($value, $adapterOptions[$key], 'Checking key '.$key);
        }
    }
}
