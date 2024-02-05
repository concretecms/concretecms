<?php

namespace Concrete\Tests\File\Service;

use Core;
use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FileTest extends TestCase
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    protected $fileHelper;

    public function setUp():void
    {
        $this->fileHelper = Core::make('helper/file');
    }

    public static function splitFilenameDataProvider()
    {
        return [
            ['simple.txt', ['', 'simple', 'txt']],
            ['.htaccess', ['', '.htaccess', '']],
            ['/simple.txt', ['/', 'simple', 'txt']],
            ['\\simple.txt', ['\\', 'simple', 'txt']],
            ['/path/only/', ['/path/only/', '', '']],
            ['/path/with.dots/', ['/path/with.dots/', '', '']],
            ['/path/with.dots/base.ext', ['/path/with.dots/', 'base', 'ext']],
            ['/path/with.dots/base', ['/path/with.dots/', 'base', '']],
            ['/path/with.dots/.gitignore', ['/path/with.dots/', '.gitignore', '']],
        ];
    }

    /**
     * @param mixed $full
     * @param mixed $splitted
     */
    #[DataProvider('splitFilenameDataProvider')]
    public function testSplitFilename($full, $splitted)
    {
        $this->assertSame($splitted, $this->fileHelper->splitFilename($full));
    }
}
