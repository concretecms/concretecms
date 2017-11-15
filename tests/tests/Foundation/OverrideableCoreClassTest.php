<?php

namespace Concrete\Tests\Foundation;

use Concrete\Core\Foundation\Environment;
use Concrete\Core\Support\Facade\Facade;
use Concrete\TestHelpers\Foundation\ClassLoaderTestCase;

class OverrideableCoreClassTest extends ClassLoaderTestCase
{
    public function coreClassCoreDataProvider()
    {
        return [
            ['Block\Autonav\Controller', '\Concrete\Block\Autonav\Controller'],
            ['Block\CoreAreaLayout\Controller', '\Concrete\Block\CoreAreaLayout\Controller'],
            ['Core\Attribute\Key\UserKey', '\Concrete\Core\Attribute\Key\UserKey'],
            ['Attribute\Select\Controller', '\Concrete\Attribute\Select\Controller'],
            ['Authentication\Facebook\Controller', '\Concrete\Authentication\Facebook\Controller'],
            ['Core\File\StorageLocation\Configuration\DefaultConfiguration', '\Concrete\Core\File\StorageLocation\Configuration\DefaultConfiguration'],
            ['Core\Permission\Key\PageKey', '\Concrete\Core\Permission\Key\PageKey'],
            ['Core\Permission\Access\PageAccess', '\Concrete\Core\Permission\Access\PageAccess'],
            ['Core\Permission\Access\Entity\UserEntity', '\Concrete\Core\Permission\Access\Entity\UserEntity'],
            ['Core\Workflow\BasicWorkflow', '\Concrete\Core\Workflow\BasicWorkflow'],
            ['Core\Workflow\Request\DeletePageRequest', '\Concrete\Core\Workflow\Request\DeletePageRequest'],
        ];
    }

    public function coreClassPackageDataProvider()
    {
        return [
            ['Block\FancyBlock\Controller', 'my_package', '\Concrete\Package\MyPackage\Block\FancyBlock\Controller', '\Concrete\Package\MyPackage\Block\FancyBlock\Controller'],
            ['Attribute\Fancy\Controller', 'page_selector', '\Concrete\Package\PageSelector\Attribute\Fancy\Controller', '\Concrete\Package\PageSelector\Attribute\Fancy\Controller'],
            ['Core\Attribute\Key\UserKey', 'my_package', '\Concrete\Package\MyPackage\Src\Attribute\Key\UserKey', '\Concrete\Package\MyPackage\Attribute\Key\UserKey'],
            ['Core\Workflow\AwesomeWorkflow', 'my_workflow', '\Concrete\Package\MyWorkflow\Src\Workflow\AwesomeWorkflow', '\Concrete\Package\MyWorkflow\Workflow\AwesomeWorkflow'],
            ['Core\Editor\Snippet\MySnippet', 'text_snippets', '\Concrete\Package\TextSnippets\Src\Editor\Snippet\MySnippet', '\Concrete\Package\TextSnippets\Editor\Snippet\MySnippet'],
        ];
    }

    public function coreClassApplicationDataProvider()
    {
        return [
            ['Block\FancyBlock\Controller', '\Application\Block\FancyBlock\Controller', '\Application\Block\FancyBlock\Controller'],
            ['Core\Attribute\Key\UserKey', '\Application\Src\Attribute\Key\UserKey', '\Application\Concrete\Attribute\Key\UserKey'],
            ['Attribute\Select\Controller', '\Application\Attribute\Select\Controller', '\Application\Attribute\Select\Controller'],
            ['Block\ExpressForm\Controller', '\Application\Block\ExpressForm\Controller', '\Application\Block\ExpressForm\Controller'],
        ];
    }

    public function overrideableCoreClassCoreDataProvider()
    {
        return [
            ['Core\Captcha\SecurimageController', DIRNAME_CLASSES . '/Captcha/SecurimageController.php', '\Concrete\Core\Captcha\SecurimageController'],
            ['Core\Cache\Page\FilePageCache', DIRNAME_CLASSES . '/Cache/Page/FilePageCache.php', '\Concrete\Core\Cache\Page\FilePageCache'],
            ['Job\IndexSearch', DIRNAME_JOBS . '/index_search.php', '\Concrete\Job\IndexSearch'],
            ['MenuItem\ClearCache\Controller', DIRNAME_MENU_ITEMS . '/clear_cache/controller.php', '\Concrete\MenuItem\ClearCache\Controller'],
        ];
    }

    public function overrideableCoreClassPackageDataProvider()
    {
        return [
            ['recaptcha', 'Core\Captcha\RecaptchaController', DIRNAME_CLASSES . '/Captcha/RecaptchaController.php',
                '\Concrete\Package\Recaptcha\Src\Captcha\RecaptchaController', '\Concrete\Package\Recaptcha\Captcha\RecaptchaController', ],
            ['clear_cache', 'MenuItem\ClearCache\Controller', DIRNAME_MENU_ITEMS . '/clear_cache/controller.php',
                '\Concrete\Package\ClearCache\MenuItem\ClearCache\Controller', '\Concrete\Package\ClearCache\MenuItem\ClearCache\Controller', ],
            ['varnish', 'Core\Cache\Page\VarnishPageCache', DIRNAME_CLASSES . '/Cache/Page/VarnishPageCache.php', '\Concrete\Package\Varnish\Src\Cache\Page\VarnishPageCache', '\Concrete\Package\Varnish\Cache\Page\VarnishPageCache'],
            ['mail_users', 'Job\MailUsers', DIRNAME_JOBS . '/mail_users.php', '\Concrete\Package\MailUsers\Job\MailUsers', '\Concrete\Package\MailUsers\Job\MailUsers'],
        ];
    }

    public function overrideableCoreClassApplicationOverrideDataProvider()
    {
        return [
            ['Job\IndexSearch', DIRNAME_JOBS . '/index_search.php', '\Application\Job\IndexSearch', '\Application\Job\IndexSearch'],
            ['Core\Captcha\SecurimageController', DIRNAME_CLASSES . '/Captcha/SecurimageController.php', '\Application\Src\Captcha\SecurimageController', '\Application\Concrete\Captcha\SecurimageController'],
        ];
    }

    /**
     * @dataProvider coreClassCoreDataProvider()
     *
     * @param mixed $fragment
     * @param mixed $class
     */
    public function testCoreClassCore($fragment, $class)
    {
        $this->assertEquals($class, core_class($fragment, false));
        $this->assertTrue(class_exists($class), sprintf('Class %s does not exist', $class));
    }

    /**
     * Tests both legacy and non-legacy class generation.
     *
     * @dataProvider coreClassPackageDataProvider()
     *
     * @param mixed $fragment
     * @param mixed $prefix
     * @param mixed $legacyClass
     * @param mixed $class
     */
    public function testCoreClassPackage($fragment, $prefix, $legacyClass, $class)
    {
        $legacyPackage = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $legacyPackage->expects($this->any())
            ->method('shouldEnableLegacyNamespace')
            ->will($this->returnValue(true));

        $package = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package->expects($this->any())
            ->method('shouldEnableLegacyNamespace')
            ->will($this->returnValue(false));

        $legacyService = $this->getMockBuilder('Concrete\Core\Package\PackageService')
            ->disableOriginalConstructor()
            ->getMock();
        $legacyService->expects($this->any())
            ->method('getClass')
            ->will($this->returnValueMap([
                [$prefix, $legacyPackage],
            ])
        );

        $service = $this->getMockBuilder('Concrete\Core\Package\PackageService')
            ->disableOriginalConstructor()
            ->getMock();
        $service->expects($this->any())
            ->method('getClass')
            ->will($this->returnValueMap([
                [$prefix, $package],
            ])
        );

        $app = Facade::getFacadeApplication();
        $origService = $app->make('Concrete\Core\Package\PackageService');

        $app['Concrete\Core\Package\PackageService'] = $legacyService;

        $this->assertEquals($legacyClass, core_class($fragment, $prefix));

        $app['Concrete\Core\Package\PackageService'] = $service;

        $this->assertEquals($class, core_class($fragment, $prefix));

        $app['Concrete\Core\Package\PackageService'] = $origService;
    }

    /**
     * @dataProvider coreClassApplicationDataProvider()
     *
     * @param mixed $fragment
     * @param mixed $legacyClass
     * @param mixed $class
     */
    public function testCoreClassApplication($fragment, $legacyClass, $class)
    {
        \Config::save('app.enable_legacy_src_namespace', false);
        $this->assertEquals($class, core_class($fragment, true));
        \Config::save('app.enable_legacy_src_namespace', true);
        $this->assertEquals($legacyClass, core_class($fragment, true));
        \Config::save('app.enable_legacy_src_namespace', false);
    }

    /**
     * @dataProvider overrideableCoreClassCoreDataProvider()
     *
     * @param mixed $fragment
     * @param mixed $path
     * @param mixed $class
     */
    public function testOverrideableCoreClassCore($fragment, $path, $class)
    {
        $this->assertEquals($class, overrideable_core_class($fragment, $path, false));
    }

    /**
     * Tests both legacy and non-legacy class generation.
     *
     * @dataProvider overrideableCoreClassPackageDataProvider()
     *
     * @param mixed $pkgHandle
     * @param mixed $fragment
     * @param mixed $path
     * @param mixed $legacyClass
     * @param mixed $class
     */
    public function testOverrideableCoreClassPackage($pkgHandle, $fragment, $path, $legacyClass, $class)
    {
        $legacyPackage = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $legacyPackage->expects($this->any())
            ->method('shouldEnableLegacyNamespace')
            ->will($this->returnValue(true));

        $package = $this->getMockBuilder('Concrete\Core\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package->expects($this->any())
            ->method('shouldEnableLegacyNamespace')
            ->will($this->returnValue(false));

        $legacyService = $this->getMockBuilder('Concrete\Core\Package\PackageService')
            ->disableOriginalConstructor()
            ->getMock();
        $legacyService->expects($this->any())
            ->method('getClass')
            ->will($this->returnValueMap([
                [$pkgHandle, $legacyPackage],
            ])
            );

        $service = $this->getMockBuilder('Concrete\Core\Package\PackageService')
            ->disableOriginalConstructor()
            ->getMock();
        $service->expects($this->any())
            ->method('getClass')
            ->will($this->returnValueMap([
                [$pkgHandle, $package],
            ])
            );

        $app = Facade::getFacadeApplication();
        $origService = $app->make('Concrete\Core\Package\PackageService');

        $app['Concrete\Core\Package\PackageService'] = $legacyService;

        $this->assertEquals($legacyClass, overrideable_core_class($fragment, $path, $pkgHandle));

        $app['Concrete\Core\Package\PackageService'] = $service;

        $this->assertEquals($class, overrideable_core_class($fragment, $path, $pkgHandle));

        $app['Concrete\Core\Package\PackageService'] = $origService;
    }

    /**
     * @dataProvider overrideableCoreClassApplicationOverrideDataProvider()
     *
     * @param mixed $fragment
     * @param mixed $path
     * @param mixed $legacyClass
     * @param mixed $class
     */
    public function testOverrideableCoreClassApplicationOverride($fragment, $path, $legacyClass, $class)
    {
        $path = trim($path, '/');
        $env = Environment::get();
        $env->clearOverrideCache();

        $destinationDirectory = DIR_APPLICATION . '/' . dirname($path);
        $this->putFileIntoPlace($path, $destinationDirectory);

        \Config::save('app.enable_legacy_src_namespace', false);
        $this->assertEquals($class, core_class($fragment, true));
        \Config::save('app.enable_legacy_src_namespace', true);
        $this->assertEquals($legacyClass, core_class($fragment, true));
        \Config::save('app.enable_legacy_src_namespace', false);

        $this->cleanUpFile(DIR_APPLICATION, $path);
    }
}
