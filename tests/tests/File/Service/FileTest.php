<?php
namespace Concrete\Tests\Core\File\Service;

use Core;

class FileTest extends \PHPUnit_Framework_TestCase
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
        return array(
            array('simple.txt', array('', 'simple', 'txt')),
            array('.htaccess', array('', '.htaccess', '')),
            array('/simple.txt', array('/', 'simple', 'txt')),
            array('\\simple.txt', array('\\', 'simple', 'txt')),
            array('/path/only/', array('/path/only/', '', '')),
            array('/path/with.dots/', array('/path/with.dots/', '', '')),
            array('/path/with.dots/base.ext', array('/path/with.dots/', 'base', 'ext')),
            array('/path/with.dots/base', array('/path/with.dots/', 'base', '')),
            array('/path/with.dots/.gitignore', array('/path/with.dots/', '.gitignore', '')),
        );
    }

    /**
     * @dataProvider splitFilenameDataProvider
     */
    public function testSplitFilename($full, $splitted)
    {
        $this->assertSame($splitted, $this->fileHelper->splitFilename($full));
    }
}
