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
use Concrete\Core\File\Exception\RequestTimeoutException;

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
    public function testTimeoutOptions($adapterClass)
    {
        $this->checkValidAdapter($adapterClass, true);
        // URL of a big file
        $client = self::$app->make(Factory::class)->createFromOptions([
            'ssltransport' => 'tls',
            'sslverifypeer' => false,
            'connectiontimeout' => 5,
            'executetimeout' => 1,
        ], $adapterClass);
        $error = null;
        try {
            $client->setMethod('GET')->setUri('https://github.com/concrete5/concrete5/archive/8.0.zip')->send();
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertTrue($error !== null, 'Trying to download a big file with a tiny executetimeout should fail');
        $this->assertTrue(get_class($error) === RequestTimeoutException::class);
    }
}
