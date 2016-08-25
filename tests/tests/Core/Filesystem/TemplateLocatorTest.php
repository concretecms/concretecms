<?php
namespace Concrete\Tests\Core\Html\Service;

use Concrete\Core\Filesystem\TemplateLocator;

class TemplateLocatorTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/address/composer.php');
        $location = $locator->getLocation();

        $this->assertInstanceOf('Concrete\Core\Foundation\EnvironmentRecord', $location);
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/address/composer.php', $location->getFile());
    }

    public function testFallback()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/address/test.php');
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/address/form.php');
        $location = $locator->getLocation();

        $this->assertInstanceOf('Concrete\Core\Foundation\EnvironmentRecord', $location);
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/address/form.php', $location->getFile());
    }

    public function testPackagedAttribute()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/address/custom_form.php', 'foo_package');
        $location = $locator->getLocation();

        $this->assertInstanceOf('Concrete\Core\Foundation\EnvironmentRecord', $location);
        $this->assertEquals(DIR_BASE . '/packages/foo_package/attributes/address/custom_form.php', $location->getFile());
        $this->assertEquals(false, $location->exists());
    }

    public function testHigherPriorityLocation()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ATTRIBUTES . '/address/form.php');
        $location = $locator->getLocation();

        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/address/form.php', $location->getFile());

        $locator->prependLocation(DIRNAME_ATTRIBUTES . '/address/composer.php');
        $location = $locator->getLocation();

        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/address/composer.php', $location->getFile());

        $locator->prependLocation(DIRNAME_ATTRIBUTES . '/address/custom_composer.php', 'stupid_package');
        $location = $locator->getLocation();

        // Should still be the same file because the finally added one doesn't exist
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES . '/address/composer.php', $location->getFile());
    }


}
