<?php

namespace Concrete\Tests\Filesystem;

use Concrete\Core\Filesystem\Template;
use Concrete\Core\Filesystem\TemplateLocator;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;

class TemplateLocatorTest extends PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_HANDLE = 'test_attribute';

    protected function setUp()
    {
        $fs = new Filesystem();
        $dir = DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE;
        if (!$fs->isDirectory($dir)) {
            $fs->makeDirectory($dir);
        }
        foreach (['composer.php', 'form.php'] as $template) {
            file_put_contents($dir . '/' . $template, '');
        }
    }

    protected function tearDown()
    {
        $fs = new Filesystem();
        $dir = DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE;
        if ($fs->isDirectory($dir)) {
            $fs->deleteDirectory($dir);
        }
    }

    public function testBasic()
    {
        $template = new Template('composer');
        $locator = new TemplateLocator($template);
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE);
        $location = $locator->getLocation();

        $this->assertInstanceOf('Concrete\Core\Filesystem\FileLocator\Record', $location);
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/composer.php', $location->getFile());
    }

    public function testFallback()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/test.php');
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/form.php');
        $location = $locator->getLocation();

        $this->assertInstanceOf('Concrete\Core\Filesystem\FileLocator\Record', $location);
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/form.php', $location->getFile());
    }

    public function testPackagedAttribute()
    {
        $locator = new TemplateLocator();
        $locator->addLocation([DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/custom_form.php', 'foo_package']);
        $location = $locator->getLocation();

        $this->assertInstanceOf('Concrete\Core\Filesystem\FileLocator\Record', $location);
        $this->assertEquals(DIR_BASE . '/packages/foo_package/attributes/' . static::ATTRIBUTE_HANDLE . '/custom_form.php', $location->getFile());
        $this->assertEquals(false, $location->exists());
    }

    public function testHigherPriorityLocation()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/form.php');
        $location = $locator->getLocation();

        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/form.php', $location->getFile());

        $locator->prependLocation(DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/composer.php');
        $location = $locator->getLocation();

        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/composer.php', $location->getFile());

        $locator->prependLocation(DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/custom_composer.php', 'stupid_package');
        $location = $locator->getLocation();

        // Should still be the same file because the finally added one doesn't exist
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/' . static::ATTRIBUTE_HANDLE . '/composer.php', $location->getFile());
    }
}
