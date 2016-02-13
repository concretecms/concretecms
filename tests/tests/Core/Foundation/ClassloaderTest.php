<?php
namespace Concrete\Tests\Core\Foundation;

use Concrete\Core\Entity\Package;
use Concrete\Core\Foundation\ClassLoader;
use Loader;
use Core;
use Environment;

class ClassloaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->obj = \Concrete\Core\Foundation\Classloader::getInstance();
    }

    public function testPsr4AutoloadingCore()
    {
        $this->assertTrue(class_exists('\Concrete\Core\Foundation\Object'));
        $this->assertTrue(class_exists('\Concrete\Core\Application\Application'));
        $this->assertTrue(class_exists('\Concrete\Core\Http\Request'));
    }

    public function testThemeAutoloadingCore()
    {
        $this->assertTrue(class_exists('\Concrete\Theme\Elemental\PageTheme'));
        $this->assertTrue(class_exists('\Concrete\Theme\Concrete\PageTheme'));
    }

    public function testStartingPointPackageAutoloading()
    {
        $this->assertTrue(class_exists('\Concrete\StartingPointPackage\ElementalBlank\Controller'));
    }

    public function testJobAutoloadingCore()
    {
        $this->assertTrue(class_exists('\Concrete\Job\IndexSearchAll'));
        $this->assertTrue(class_exists('\Concrete\Job\IndexSearch'));
        $this->assertTrue(class_exists('\Concrete\Job\GenerateSitemap'));
        $this->assertTrue(class_exists('\Concrete\Job\UpdateGatherings'));
        $this->assertTrue(class_exists('\Concrete\Job\IndexSearchAll'));
        $this->assertTrue(class_exists('\Concrete\Job\RemoveOldPageVersions'));
        $this->assertTrue(class_exists('\Concrete\Job\ProcessEmail'));
    }

    public function testOverrideableCoreClassesCore()
    {
        $c = new \Page();
        $this->assertTrue($c instanceof \Concrete\Core\Page\Page);
    }

    public function testRouteController()
    {
        $request = new \Concrete\Core\Http\Request();
        $request->attributes->set('_controller', '\Concrete\Controller\Install::view');
        $resolver = \Core::make('Concrete\Core\Controller\ApplicationAwareControllerResolver');
        $callback = $resolver->getController($request);
        $this->assertTrue($callback[0] instanceof \Concrete\Controller\Install);

        $request = new \Concrete\Core\Http\Request();
        $request->attributes->set('_controller', '\Concrete\Controller\Panel\Page\Design::preview_contents');
        $resolver = \Core::make('Concrete\Core\Controller\ApplicationAwareControllerResolver');
        $callback = $resolver->getController($request);
        $this->assertTrue($callback[0] instanceof \Concrete\Controller\Panel\Page\Design);
    }

    public function testCoreClassFunction()
    {
        $class = core_class('\Block\TestBlock\Controller');
        $this->assertEquals('\Concrete\Block\TestBlock\Controller', $class);

        $class = core_class('\Block\TestBlock\Controller', 'foo_bar');
        $this->assertEquals('\Concrete\Package\FooBar\Block\TestBlock\Controller', $class);

        $class = overrideable_core_class('\Src\Captcha\AkismetController', '/foo', 'akismet');
        $this->assertEquals('\Concrete\Package\Akismet\Src\Captcha\AkismetController', $class);

        // now for the weird one.
        // We need to already have these files included so that the autoloader doesn't break
        require 'fixtures/FakeAkismetPackageController.php';
        require 'fixtures/FakeCalendarPackageController.php';
        $class = overrideable_core_class('\Core\Captcha\AkismetController', '/foo', 'akismet');
        $this->assertEquals('\Concrete\Package\Akismet\Src\Captcha\AkismetController', $class);

        $class = core_class('Core\\Attribute\\Key\\EventKey', 'calendar');
        $this->assertEquals('\\Concrete\\Package\\Calendar\\Src\\Attribute\\Key\\EventKey', $class);
    }

    public function testRouteControllerOverride()
    {
        $root = dirname(DIR_BASE_CORE . '../');
        mkdir($root . '/application/controllers/panel/page/', 0777, true);
        copy(dirname(__FILE__) . '/fixtures/design.php', $root . '/application/controllers/panel/page/design.php');

        Core::bind('\Concrete\Controller\Panel\Page\Design', function () {
            return new \Application\Controller\Panel\Page\Design();
        });

        $request = new \Concrete\Core\Http\Request();
        $request->attributes->set('_controller', '\Concrete\Controller\Panel\Page\Design::preview_contents');
        $resolver = \Core::make('Concrete\Core\Controller\ApplicationAwareControllerResolver');
        $callback = $resolver->getController($request);

        unlink($root . '/application/controllers/panel/page/design.php');
        rmdir($root . '/application/controllers/panel/page');
        rmdir($root . '/application/controllers/panel');

        $this->assertTrue($callback[0] instanceof \Application\Controller\Panel\Page\Design);
        $this->assertTrue($callback[0] instanceof \Concrete\Controller\Panel\Page\Design);
    }

    public function testAttributes()
    {
        $at = new \Concrete\Core\Attribute\Type();
        $at->atHandle = 'boolean';
//        $at->loadController();
        $this->assertTrue(class_exists('\Concrete\Attribute\Boolean\Controller'));
    }

    public function testBlocks()
    {
        $bt = new \BlockType();
        $bt->setBlockTypeHandle('core_stack_display');
        $class = $bt->getBlockTypeClass();
        $classExists = class_exists($class);
        $this->assertTrue($class == '\Concrete\Block\CoreStackDisplay\Controller');
        $this->assertTrue($classExists);
    }

    public function testBlockControllerOverride()
    {
        $env = Environment::get();
        $env->clearOverrideCache();

        $root = dirname(DIR_BASE_CORE . '../');
        mkdir($root . '/application/blocks/core_area_layout/', 0777, true);
        copy(dirname(__FILE__) . '/fixtures/CoreAreaLayoutController.php', $root . '/application/blocks/core_area_layout/controller.php');

        $bt = new \BlockType();
        $bt->setBlockTypeHandle('core_area_layout');
        $class = $bt->getBlockTypeClass();
        $classExists = class_exists($class);

        unlink($root . '/application/blocks/core_area_layout/controller.php');
        rmdir($root . '/application/blocks/core_area_layout');

        $this->assertTrue($class == '\Application\Block\CoreAreaLayout\Controller');
        $this->assertTrue($classExists);
    }

    public function testPackageSrcFiles()
    {
        $env = Environment::get();
        $env->clearOverrideCache();

        require 'fixtures/testing.php';
        $package = new \Concrete\Package\Testing\Controller(\Core::make('app'));
        $loader = ClassLoader::getInstance();
        $loader->registerPackage($package);

        $root = dirname(DIR_BASE_CORE . '../');
        @mkdir($root . '/packages/testing/src/', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/RouteHelper.php', $root . '/packages/testing/src/RouteHelper.php');

        $class = new \Concrete\Package\Testing\Src\RouteHelper();
        $this->assertInstanceOf('\Concrete\Package\Testing\Src\RouteHelper', $class);

        @unlink($root . '/packages/testing/src/RouteHelper.php');
        @rmdir($root . '/packages/testing/src');
        @rmdir($root . '/packages/testing');
    }

    public function testLegacyHelpers()
    {
        $fh = Loader::helper('file');
        $vh = Loader::helper('validation/error');
        $this->assertTrue($fh instanceof \Concrete\Core\File\Service\File);
        $this->assertTrue($vh instanceof \Concrete\Core\Error\Error);
    }

    public function testUpgradedPackageSrcFiles()
    {
        $env = Environment::get();
        $env->clearOverrideCache();

        require 'fixtures/amazing_power.php';
        $package = new \Concrete\Package\AmazingPower\Controller(\Core::make("app"));
        $loader = ClassLoader::getInstance();
        $loader->registerPackage($package);

        $root = dirname(DIR_BASE_CORE . '../');
        @mkdir($root . '/packages/amazing_power/src/ElectricState/Routing', 0777, true);
        @mkdir($root . '/packages/amazing_power/src/Concrete/Captcha', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/amazing_power/RouteHelper.php', $root . '/packages/amazing_power/src/ElectricState/Routing/RouteHelper.php');
        @copy(dirname(__FILE__) . '/fixtures/amazing_power/AkismetController.php', $root . '/packages/amazing_power/src/Concrete/Captcha/AkismetController.php');
        $class = overrideable_core_class('\Captcha\AkismetController', 'Captcha/AkismetController.php', $package->getPackageHandle());
        $this->assertEquals('\Concrete\Package\AmazingPower\Captcha\AkismetController', $class);
        $this->assertTrue(class_exists('\Concrete\Package\AmazingPower\Captcha\AkismetController'));
        $this->assertTrue(class_exists('\ElectricState\AmazingPower\Routing\RouteHelper'));

        @unlink($root . '/packages/amazing_power/src/ElectricState/Routing/RouteHelper.php');
        @unlink($root . '/packages/amazing_power/src/Concrete/Captcha/AkismetController.php');
        @rmdir($root . '/packages/amazing_power/src/ElectricState/Routing');
        @rmdir($root . '/packages/amazing_power/src/ElectricState');
        @unlink($root . '/packages/amazing_power/src/Concrete/Captcha');
        @unlink($root . '/packages/amazing_power/src/Concrete');
        @unlink($root . '/packages/amazing_power/src');
        @unlink($root . '/packages/amazing_power');
    }
}
