<?php

namespace Concrete\Tests\File\Service;

use Core;
use PHPUnit_Framework_TestCase;

class FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    protected $fileHelper;

    protected function setUp()
    {
        $this->fileHelper = Core::make('helper/file');
    }

    public function splitFilenameDataProvider()
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
     * @dataProvider splitFilenameDataProvider
     *
     * @param mixed $full
     * @param mixed $splitted
     */
    public function testSplitFilename($full, $splitted)
    {
        $this->assertSame($splitted, $this->fileHelper->splitFilename($full));
    }
}
