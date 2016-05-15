<?php
namespace Concrete\Tests\Core\Foundation;

use Concrete\Core\Application\Application as ServiceLocator;
use Concrete\Core\Foundation\Service\ProviderList;

class TestClass
{
}

class TestClass2
{
}

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sl = new ServiceLocator();
    }

    public function testRegisterAndRetrieveNewClass()
    {
        $this->sl->bind('testclass', function () {
            return new \Concrete\Tests\Core\Foundation\TestClass();
        });

        $tc = $this->sl->make('testclass');
        $this->assertTrue($tc instanceof \Concrete\Tests\Core\Foundation\TestClass, sprintf('$tc did not match test class. Instead its class was %s', get_class($tc)));

        $tc1 = $this->sl->make('testclass');
        $tc2 = $this->sl->make('testclass');
        $this->assertTrue($tc1 !== $tc2);
    }

    public function testSingletons()
    {
        $this->sl->singleton('testclass2', function () {
            return new \Concrete\Tests\Core\Foundation\TestClass2();
        });

        $tc1 = $this->sl->make('testclass2');
        $tc2 = $this->sl->make('testclass2');
        $this->assertTrue($tc1 === $tc2);
    }

    public function testInstance()
    {
        $o = new \stdClass();
        $o->firstname = 'Andrew';
        $o->lastname = 'Embler';
        $this->sl->instance('user', $o);

        $this->_finishInstanceTest();
    }

    protected function _finishInstanceTest()
    {
        $user = $this->sl->make('user');
        $this->assertTrue($user->firstname == 'Andrew' && $user->lastname == 'Embler');
    }

    public function testClassDefine()
    {
        $pt = $this->sl->make('\Concrete\Core\Page\Theme\Theme');
        $this->assertTrue($pt instanceof \Concrete\Core\Page\Theme\Theme);

        require_once 'fixtures/FakeThemePackageController.php';

        $class1 = core_class('Core\Page\Theme\Theme');
        $class2 = core_class('Core\Page\Theme\RiverTheme', 'river_theme');
        $class3 = core_class('Core\Page\Theme\Theme', true);

        $this->assertTrue($class1 == '\Concrete\Core\Page\Theme\Theme', 'class1 == ' . $class1);
        $this->assertTrue($class2 == '\Concrete\Package\RiverTheme\Src\Page\Theme\RiverTheme', 'class2 == ' . $class2);
        $this->assertTrue($class3 == '\Application\Src\Page\Theme\Theme', 'class3 == ' . $class3);
    }

    public function testServiceProviders()
    {
        $provider = new \Concrete\Core\Validation\ValidationServiceProvider($this->sl);
        $provider->register();

        $this->assertTrue($this->sl->bound('helper/validation/antispam'));
        $bw1 = $this->sl->make('helper/validation/banned_words');
        $bw2 = $this->sl->make('helper/validation/banned_words');
        $this->assertTrue($bw1 === $bw2);

        // test a non singleton.
        $vt1 = $this->sl->make('helper/validation/token');
        $vt2 = $this->sl->make('helper/validation/token');
        $this->assertFalse($vt1 === $vt2);
        $this->assertTrue($vt1 == $vt2);
    }

    public function testOverrides()
    {
        require 'fixtures/MyFile.php';

        $this->sl->bind('file', '\Concrete\Core\File\Service\File');

        $filehelper1 = $this->sl->make('file');
        $this->sl->bind('file', function () {
            return new \Application\Src\My\File();
        });

        $filehelper2 = $this->sl->make('file');
        $this->assertTrue($filehelper1 instanceof \Concrete\Core\File\Service\File);
    }

    public function testUnregistered()
    {
        require 'fixtures/AutonavController.php';

        $controller = $this->sl->make('Concrete\Block\Autonav\Controller');
        $this->assertTrue($controller instanceof \Concrete\Block\Autonav\Controller);

        $mockBlock = new \stdClass();
        $this->sl->bind('Concrete\Block\Autonav\Controller', function () use ($mockBlock) {
            return new \Application\Block\Autonav\Controller($mockBlock);
        });

        $controller2 = $this->sl->make('Concrete\Block\Autonav\Controller');
        $this->assertTrue($controller2 instanceof \Concrete\Block\Autonav\Controller);
    }

    public function testAllServiceProviders()
    {
        $groups = array(
            '\Concrete\Core\File\FileServiceProvider',
            '\Concrete\Core\Encryption\EncryptionServiceProvider',
            '\Concrete\Core\Validation\ValidationServiceProvider',
            '\Concrete\Core\Localization\LocalizationServiceProvider',
            '\Concrete\Core\Feed\FeedServiceProvider',
            '\Concrete\Core\Html\HtmlServiceProvider',
            '\Concrete\Core\Mail\MailServiceProvider',
            '\Concrete\Core\Application\ApplicationServiceProvider',
            '\Concrete\Core\Utility\UtilityServiceProvider',
            '\Concrete\Core\Database\DatabaseServiceProvider',
            '\Concrete\Core\Form\FormServiceProvider',
            '\Concrete\Core\Session\SessionServiceProvider',
            '\Concrete\Core\Http\HttpServiceProvider',
            '\Concrete\Core\Events\EventsServiceProvider',
        );

        $gr = new ProviderList($this->sl);
        $gr->registerProviders($groups);

        $this->assertTrue($this->sl->bound('helper/concrete/ui'));
        $this->assertTrue($this->sl->bound('helper/concrete/ui/help'));
        $this->assertTrue($this->sl->bound('helper/concrete/asset_library'));
        $this->assertTrue($this->sl->bound('helper/mime'));
        $this->assertTrue($this->sl->bound('director'));
    }
}
