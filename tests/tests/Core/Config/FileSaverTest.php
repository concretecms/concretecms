<?php

use Concrete\Core\Config\FileSaver;
use Illuminate\Filesystem\Filesystem;

class FileSaverTest extends PHPUnit_Framework_TestCase
{
    /** @var FileSaver */
    protected $saver;

    /** @var FileSystem */
    protected $files;

    public function setUp()
    {
        $this->saver = new \Concrete\Tests\Core\Config\Fixtures\TestFileSaver($this->files = new Filesystem());
    }

    public function testSavingArray()
    {
        $group = md5(time() . uniqid());

        $this->saver->save('test.array', array(1, 2), 'testing', $group);
        $this->saver->save('test.array', array(1), 'testing', $group);

        $path = DIR_TESTS . "/config/generated_overrides/{$group}.php";
        $contents = @include_once $path;

        $this->files->delete($path);

        $this->assertEquals(array(1), array_get($contents, 'test.array'), "Saver doesn't save correctly");
    }

    public function testSavingConfig()
    {
        $group = md5(time() . uniqid());
        $item = 'this.is.the.test.key';
        $value = $group;

        $this->saver->save($item, $value, 'testing', $group);

        $path = DIR_TESTS . "/config/generated_overrides/{$group}.php";
        $exists = $this->files->exists($path);

        $array = array();
        if ($exists) {
            $array = $this->files->getRequire($path);
            $this->files->delete($path);
        }

        $this->assertTrue($exists, 'Failed to save file');
        $this->assertEquals($value, array_get($array, $item), 'Failed to save correct value.');
    }

    public function testSavingNamespacedConfig()
    {
        $group = md5(time() . uniqid());
        $namespace = md5(time() . uniqid());
        $item = 'this.is.the.test.key';
        $value = $group;

        $this->saver->save($item, $value, 'testing', $group, $namespace);

        $path = DIR_TESTS . "/config/generated_overrides/{$namespace}/{$group}.php";
        $exists = $this->files->exists($path);

        $array = array();
        if ($exists) {
            $array = $this->files->getRequire($path);
            $this->files->delete($path);
        }

        $this->assertTrue($exists, 'Failed to save file');
        $this->assertEquals($value, array_get($array, $item), 'Failed to save correct value.');
    }
}
