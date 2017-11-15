<?php

namespace Concrete\Tests\File\Service;

use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Support\Facade\Application;
use PHPUnit_Framework_TestCase;

class VolatileDirectoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    public static function setupBeforeClass()
    {
        self::$app = Application::getFacadeApplication();
    }

    public function testVolatileDirectoryExplicitUnset()
    {
        $vd = self::$app->make(VolatileDirectory::class);
        $fs = $vd->getFilesystem();
        $path = $vd->getPath();
        $this->assertTrue($fs->isDirectory($path));
        unset($vd);
        $this->assertFalse($fs->isDirectory($path));
    }

    public function testVolatileDirectoryImplicitUnset()
    {
        list($fs, $path) = $this->createVolatileDirectory();
        $this->assertFalse($fs->isDirectory($path));
    }

    private function createVolatileDirectory()
    {
        $vd = self::$app->make(VolatileDirectory::class);
        /* @var VolatileDirectory $vd */
        $fs = $vd->getFilesystem();
        $path = $vd->getPath();
        $this->assertTrue($fs->isDirectory($path));
        $this->assertTrue($fs->makeDirectory($path . '/subfolder'));
        $this->assertTrue($fs->put($path . '/subfolder/file.txt', 'contents') !== false);

        return [$fs, $path];
    }
}
