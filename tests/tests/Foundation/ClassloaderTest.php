<?php

namespace Concrete\Tests\Foundation;

use Concrete\Core\Foundation\ClassLoader;
use Concrete\TestHelpers\Foundation\ClassLoaderTestCase;

class ClassloaderTest extends ClassLoaderTestCase
{
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $coreAutoloader = ClassLoader::getInstance();
        $coreAutoloader->reset();
    }

    public function setUp()
    {
        parent::setUp();
        $coreAutoloader = ClassLoader::getInstance();
        $coreAutoloader->disable();
    }

    public function tearDown()
    {
        parent::tearDown();
        $coreAutoloader = ClassLoader::getInstance();
        $coreAutoloader->enable();
    }

    public function aliasesClassesDataProvider()
    {
        return [
            ['\Controller', '\Concrete\Core\Controller\Controller'],
            ['\Page', '\Concrete\Core\Page\Page'],
            ['\File', '\Concrete\Core\File\File'],
        ];
    }

    /**
     * @dataProvider aliasesClassesDataProvider
     *
     * @param mixed $a
     * @param mixed $b
     */
    public function testClassAliases($a, $b)
    {
        // We still need to enable class loaders here otherwise the aliases won't work

        $loader = ClassLoader::getInstance();
        $loader->enable();

        $class1 = new $a();
        $class2 = new $b();
        $this->assertEquals($class1, $class2);
    }

    public function applicationClassesDataProvider()
    {
        return [
            // Overrides, Modified autoloader

            ['page_theme.php', 'themes/fancy/', 'Application\Theme\Fancy\PageTheme'],
            ['autonav/controller.php', 'blocks/autonav/', 'Application\Block\Autonav\Controller'],
            ['core_area_layout/controller.php', 'blocks/core_area_layout/', 'Application\Block\CoreAreaLayout\Controller'],
            ['design.php', 'controllers/panel/page/', 'Application\Controller\Panel\Page\Design'],

            // Overrides, Strict autoloader
            ['RecaptchaController.php', 'src/Concrete/Captcha/', 'Application\Concrete\Captcha\RecaptchaController'],

            // Entity Classes
            ['TestEntity.php', 'src/Entity/', 'Application\Entity\TestEntity'],

            // Test that application/src/Whatever does NOT work
            ['TestClass.php', 'src/Testing/', 'Application\Src\Testing\TestClass', false],
            ['TestClass.php', 'src/Testing/', 'Application\Testing\TestClass', false],
        ];
    }

    public function packageClassesDataProvider()
    {
        return [
            // Overrides, Modified autoloader

            ['foo_bar', 'packages/foo_bar/controller.php', '/foo_bar/', 'Concrete\Package\FooBar\Controller'],
            ['foo_bar', 'packages/foo_bar/controller.php', '/foo_bar/', 'Concrete\Package\FooBar\DummyLoader', false],
            ['amazing_power', 'packages/amazing_power/controller.php', '/amazing_power/', 'Concrete\Package\AmazingPower\Controller'],
            ['amazing_power', 'packages/amazing_power/blocks/testing/controller.php', '/amazing_power/blocks/testing/', 'Concrete\Package\AmazingPower\Block\Testing\Controller'],
            ['amazing_power', 'packages/amazing_power/themes/amazing_fancy/page_theme.php', '/amazing_power/themes/amazing_fancy/', 'Concrete\Package\AmazingPower\Theme\AmazingFancy\PageTheme'],

            // Package Src Files, strict autoloader
            ['fancy_snippet', 'packages/fancy_snippet/src/Concrete/Editor/FancySnippet.php', '/fancy_snippet/src/Concrete/Editor/', 'Concrete\Package\FancySnippet\Editor\FancySnippet'],
            ['fancy_snippet', 'packages/fancy_snippet/src/Funky/RedHerring.php', '/fancy_snippet/src/Funky/', 'Concrete\Package\FancySnippet\Src\Funky\RedHerring', false],
            ['authenticator', 'packages/authenticator/src/Entity/Account.php', '/authenticator/src/Entity/', 'Concrete\Package\Authenticator\Entity\Account'],
        ];
    }

    public function applicationClassesLegacyDataProvider()
    {
        return [
            ['TestClass.php', 'src/Testing/', 'Application\Src\Testing\TestClass'],
            ['File.php', 'src/File', 'Application\Src\File\File'],
        ];
    }

    public function packageClassesLegacyDataProvider()
    {
        return [
['legacy_foo', 'packages/legacy_foo/src/My/Custom.php', '/legacy_foo/src/My/', 'Concrete\Package\LegacyFoo\Src\My\Custom'],
        ];
    }

    public function applicationClassesLegacyCustomNamespaceDataProvider()
    {
        return [
            ['Foobar', 'TestCustomNamespaceClass.php', 'src/Testing/', 'Foobar\Src\Testing\TestCustomNamespaceClass'],
        ];
    }

    public function packageCustomAutoloadersDataProvider()
    {
        $package1 = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package1->expects($this->any())
            ->method('getPackageAutoloaderRegistries')
            ->will($this->returnValue([
                'src/PortlandLabs/Foo' => 'PortlandLabs\Foo',
            ]));
        $package1->expects($this->any())
            ->method('getPackageHandle')
            ->will($this->returnValue('advanced_test'));

        return [
            // Overrides, Modified autoloader

            [$package1, 'packages/advanced_test/src/', '/advanced_test/', [
                'PortlandLabs\Foo\X\File',
                'PortlandLabs\Foo\Y\Bar\File',
            ]],
        ];
    }

    public function coreClassesDataProvider()
    {
        return [
            // Strict autoloader
            ['Concrete\Core\Area\Area'],
            ['Concrete\Core\User\User'],
            ['Concrete\Core\Application\EditResponse'],
            ['Concrete\Core\Entity\File\File'],
            ['Concrete\Core\View\DialogView'],

            // Modified autoloader
            ['Concrete\Attribute\Address\Controller'],
            ['Concrete\Attribute\SocialLinks\Controller'],
            ['Concrete\Attribute\Select\Option'],
            ['Concrete\Controller\Backend\Block\Action'],
            ['Concrete\Controller\Dialog\Conversation\Subscribe'],
            ['Concrete\Controller\Frontend\Jobs'],
            ['Concrete\Controller\PageType\CoreStack'],
            ['Concrete\Controller\Panel\Add'],
            ['Concrete\Controller\Search\FileFolder'],
            ['Concrete\Controller\SinglePage\Account\Avatar'],
            ['Concrete\Controller\SinglePage\Dashboard\System\Basics\Name'],
            ['Concrete\Authentication\Concrete\Controller'],
            ['Concrete\Authentication\Facebook\Controller'],
            ['Concrete\Block\CoreAreaLayout\Controller'],
            ['Concrete\Block\ExpressForm\Controller'],
            ['Concrete\Block\SocialLinks\Controller'],
            ['Concrete\Job\IndexSearch'],
            ['Concrete\Job\ProcessEmail'],
            ['Concrete\Job\RemoveOldPageVersions'],
            ['Concrete\Theme\Elemental\PageTheme'],
            ['Concrete\Theme\Dashboard\PageTheme'],

            // Legacy autoloader
            ['Concrete\Core\Legacy\TaskPermission'],
        ];
    }

    /**
     * @dataProvider coreClassesDataProvider
     *
     * @param mixed $class
     */
    public function testCoreClassExists($class)
    {
        $this->assertTrue($this->classExists($class), sprintf('Class %s failed to load', $class));
    }

    /**
     * @dataProvider applicationClassesDataProvider
     *
     * @param mixed $file
     * @param mixed $destination
     * @param mixed $class
     * @param mixed $exists
     */
    public function testApplicationCoreOverrideAutoloader($file, $destination, $class, $exists = true)
    {
        $destination = trim($destination, '/');
        $destinationDirectory = DIR_APPLICATION . '/' . $destination;
        $this->putFileIntoPlace($file, $destinationDirectory);
        $classExists = $this->classExists($class);
        $this->cleanUpFile(DIR_APPLICATION, $destination);
        if ($exists) {
            $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
        } else {
            $this->assertFalse($classExists, sprintf('Class %s loaded', $class));
        }
    }

    /**
     * @dataProvider applicationClassesLegacyDataProvider
     *
     * @param mixed $file
     * @param mixed $destination
     * @param mixed $class
     * @param mixed $exists
     */
    public function testApplicationLegacyAutoloader($file, $destination, $class, $exists = true)
    {
        $destination = trim($destination, '/');
        $destinationDirectory = DIR_APPLICATION . '/' . $destination;
        $this->putFileIntoPlace($file, $destinationDirectory);
        $loader = ClassLoader::getInstance();
        $loader->enableLegacyNamespace();
        $loader->enable();
        $classExists = $this->classExists($class);
        $this->cleanUpFile(DIR_APPLICATION, $destination);
        if ($exists) {
            $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
        } else {
            $this->assertFalse($classExists, sprintf('Class %s loaded', $class));
        }
    }

    /**
     * @dataProvider applicationClassesLegacyCustomNamespaceDataProvider
     *
     * @param mixed $namespace
     * @param mixed $file
     * @param mixed $destination
     * @param mixed $class
     */
    public function testApplicationLegacyCustomNamespaceAutoloader($namespace, $file, $destination, $class)
    {
        $destination = trim($destination, '/');
        $destinationDirectory = DIR_APPLICATION . '/' . $destination;
        $this->putFileIntoPlace($file, $destinationDirectory);
        $loader = ClassLoader::getInstance();
        $loader->enableLegacyNamespace();
        $loader->setApplicationNamespace($namespace);
        $loader->enable();
        $classExists = $this->classExists($class);
        $this->cleanUpFile(DIR_APPLICATION, $destination);
        $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
    }

    /**
     * @dataProvider packageClassesDataProvider
     *
     * @param mixed $pkgHandle
     * @param mixed $file
     * @param mixed $destination
     * @param mixed $class
     * @param mixed $exists
     */
    public function testPackageAutoloader($pkgHandle, $file, $destination, $class, $exists = true)
    {
        $destination = trim($destination, '/');
        $destinationDirectory = DIR_PACKAGES . '/' . $destination;
        $this->putFileIntoPlace($file, $destinationDirectory);

        $loader = ClassLoader::getInstance();
        $loader->enable();

        $package = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package->expects($this->any())
            ->method('shouldEnableLegacyNamespace')
            ->will($this->returnValue(false));
        $package->expects($this->any())
            ->method('getPackageHandle')
            ->will($this->returnValue($pkgHandle));
        $loader->registerPackage($package);

        $classExists = $this->classExists($class);
        $this->cleanUpFile(DIR_PACKAGES, $destination);
        if ($exists) {
            $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
        } else {
            $this->assertFalse($classExists, sprintf('Class %s loaded', $class));
        }
    }

    /**
     * @dataProvider packageClassesLegacyDataProvider
     *
     * @param mixed $pkgHandle
     * @param mixed $file
     * @param mixed $destination
     * @param mixed $class
     * @param mixed $exists
     */
    public function testPackageLegacyAutoloader($pkgHandle, $file, $destination, $class, $exists = true)
    {
        $destination = trim($destination, '/');
        $destinationDirectory = DIR_PACKAGES . '/' . $destination;
        $this->putFileIntoPlace($file, $destinationDirectory);

        $loader = ClassLoader::getInstance();
        $loader->enable();

        $package = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package->expects($this->any())
            ->method('shouldEnableLegacyNamespace')
            ->will($this->returnValue(true));
        $package->expects($this->any())
            ->method('getPackageHandle')
            ->will($this->returnValue($pkgHandle));
        $loader->registerPackage($package);

        $classExists = $this->classExists($class);
        $this->cleanUpFile(DIR_PACKAGES, $destination);
        if ($exists) {
            $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
        } else {
            $this->assertFalse($classExists, sprintf('Class %s loaded', $class));
        }
    }

    /**
     * @dataProvider packageCustomAutoloadersDataProvider
     *
     * @param mixed $package
     * @param mixed $file
     * @param mixed $destination
     * @param mixed $classes
     * @param mixed $exists
     */
    public function testPackageCustomAutoloaders($package, $file, $destination, $classes, $exists = true)
    {
        $destination = trim($destination, '/');
        $destinationDirectory = DIR_PACKAGES . '/' . $destination;
        $this->putFileIntoPlace($file, $destinationDirectory);

        $loader = ClassLoader::getInstance();
        $loader->enable();
        $loader->registerPackage($package);

        $classExists = [];
        foreach ($classes as $class) {
            $classExists[] = $this->classExists($class);
        }

        $this->cleanUpFile(DIR_PACKAGES, $destination);
        foreach ($classExists as $return) {
            if ($exists) {
                $this->assertTrue($return, sprintf('Class %s failed to load', $class));
            } else {
                $this->assertFalse($return, sprintf('Class %s loaded', $class));
            }
        }
    }

    protected function classExists($class)
    {
        $loader = ClassLoader::getInstance();
        $loader->enable();

        return class_exists($class);
    }
}
