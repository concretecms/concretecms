<?php

namespace Concrete\Tests\Api;

use Concrete\Core\Api\ApiServiceProvider;
use Concrete\Core\Api\CryptKeyFactory;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Illuminate\Filesystem\Filesystem;
use League\OAuth2\Server\CryptKey;
use Mockery as M;
use phpseclib\Crypt\RSA;
use PHPUnit_Framework_TestCase;
use RuntimeException;

class CryptKeyFactoryTest extends PHPUnit_Framework_TestCase
{
    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private static $tempDir;
    public static function setUpBeforeClass()
    {
        self::$tempDir = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/temp';
        self::clearTemp();
    }

    public static function tearDownAfterClass()
    {
        self::clearTemp();
    }

    public function testGetCryptKey()
    {
        $realConfig = app(Repository::class);
        $keyBits = $realConfig->get('concrete.api.key.bits');
        $this->assertInternalType('int', $keyBits);
        $this->assertGreaterThan(0, $keyBits);
        $rsaKeyRaw = (new RSA())->createKey($keyBits);
        $rsaKey = [];
        foreach ($rsaKeyRaw as $k => $v) {
            $rsaKey[$k] = is_string($k) ? str_replace("\r\n", "\n", $v) : $v;
        }

        $configMock = M::mock(Repository::class);
        $dbConfigMock = M::mock(Repository::class);
        $appMock = M::mock(Application::class);
        $rsaMock = M::mock(RSA::class);

        $configMock->shouldReceive('get')->withArgs(['concrete.api.key.bits'])->times(1)->andReturn($keyBits);
        $configMock->shouldReceive('get')->withArgs(['concrete.api.key.save_path'])->times(1)->andReturn(self::$tempDir);
        $configMock->shouldReceive('get')->withArgs(['concrete.filesystem.permissions.directory'])->times(1)->andReturn($realConfig->get('concrete.filesystem.permissions.directory'));
        $configMock->shouldReceive('get')->withArgs(['concrete.api.key.ownership.set'])->times(2)->andReturn(true);
        $configMock->shouldReceive('get')->withArgs(['concrete.api.key.ownership.force'])->andReturn(true);

        $dbConfigMock->shouldReceive('get')->withArgs(['api.keypair'])->times(1)->andReturn(null);
        $dbConfigMock->shouldReceive('set')->withArgs(['api.keypair', $rsaKey])->times(1)->andReturn(null);
        $dbConfigMock->shouldReceive('save')->withArgs(['api.keypair', $rsaKey])->times(1)->andReturn(null);

        $appMock->shouldReceive('make')->withArgs(['config/database'])->times(1)->andReturn($dbConfigMock);
        $rsaMock->shouldReceive('createKey')->withArgs([$keyBits])->andReturn($rsaKeyRaw);
        $appMock->shouldReceive('make')->withArgs([RSA::class])->times(1)->andReturn($rsaMock);

        $instance = app(CryptKeyFactory::class, ['config' => $configMock, 'app' => $appMock]);
        $error = null;
        try {
            $instance->getCryptKey('invalid');
        } catch (RuntimeException $error) {
        }
        $this->assertInstanceOf(RuntimeException::class, $error);
        $publicKey = $instance->getCryptKey(ApiServiceProvider::KEY_PUBLIC);
        $this->assertInstanceOf(CryptKey::class, $publicKey);
        $this->checkCryptKey($publicKey, $rsaKey[ApiServiceProvider::KEY_PUBLIC]);
        $privateKey = $instance->getCryptKey(ApiServiceProvider::KEY_PRIVATE);
        $this->assertInstanceOf(CryptKey::class, $privateKey);
        $this->checkCryptKey($privateKey, $rsaKey[ApiServiceProvider::KEY_PRIVATE]);

        $publicKey2 = $instance->getCryptKey(ApiServiceProvider::KEY_PUBLIC);
        $this->assertSame($publicKey, $publicKey2);
        $privateKey2 = $instance->getCryptKey(ApiServiceProvider::KEY_PRIVATE);
        $this->assertSame($privateKey, $privateKey2);
    }

    private static function clearTemp()
    {
        $fs = new Filesystem();
        if ($fs->isDirectory(self::$tempDir)) {
            $fs->deleteDirectory(self::$tempDir, false);
        }
        if ($fs->exists(self::$tempDir)) {
            throw new RuntimeException('Failed to delete directory ' . self::$tempDir);
        }
    }

    private function checkCryptKey(CryptKey $cryptKey, $expectedContent)
    {
        $this->assertStringStartsWith('file://' . self::$tempDir . '/', $cryptKey->getKeyPath());
        $path = substr($cryptKey->getKeyPath(), strlen('file://'));
        $this->assertFileExists($path);
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->checkFileOwnerskipWindows($path);
        } else {
            $this->checkFileOwnerskipPosix($path);
        }
        $this->assertSame($expectedContent, file_get_contents($path));
    }

    private function checkFileOwnerskipWindows($path)
    {
        $path = str_replace('//', '\\', $path);
        $output = [];
        $rc = -1;
        exec('icacls.exe ' . escapeshellarg($path) . ' /q 2>&1', $output, $rc);
        $this->assertSame(0, $rc, implode("\n", $output));
        /*
         * Sample output:
         * filename owner:(permissions)
         *
         * Successfully processed 1 files; Failed processing 0 files
         */
        $this->assertStringStartsWith($path, str_replace('//', '\\', $output[0]));
        $perms = ltrim(substr($output[0], strlen($path)));
        $currentUser = get_current_user();
        if (empty($currentUser)) {
            $currentUser = empty($_ENV['USERNAME']) ? '' : $_ENV['USERNAME'];
        }
        if ($currentUser !== '') {
            $this->assertStringEndsWith($currentUser . ':(F)', $perms);
        } else {
            $this->assertStringEndsWith(':(F)', $perms);
        }
    }

    private function checkFileOwnerskipPosix($path)
    {
        $this->assertSame(0600, fileperms($path) & 0700);
    }
}
