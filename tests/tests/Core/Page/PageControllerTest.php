<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/10/14
 * Time: 7:47 AM.
 */
class PageControllerTest extends PageTestCase
{
    public function setUp()
    {
        parent::setUp();
        $env = \Concrete\Core\Foundation\Environment::get();
        $env->clearOverrideCache();
    }
    protected function addPage1()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID' => 1,
            'cName' => 'Test page',
            'pTemplateID' => $template->getPageTemplateID(),
        ));

        return $page;
    }

    protected function addPage2()
    {
        $home = Page::getByID(HOME_CID);
        PageType::add(array(
            'handle' => 'alternate',
            'name' => 'Alternate',
        ));
        $pt = PageType::getByID(2);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID' => 1,
            'cName' => 'Test page',
            'pTemplateID' => $template->getPageTemplateID(),
        ));

        return $page;
    }

    public function testBasicPageController()
    {
        $page = $this->addPage1();
        $controller = $page->getPageController();
        $this->assertEquals('Concrete\Core\Page\Controller\PageController', get_class($controller));
    }

    public function testPageTypeController()
    {
        $page = $this->addPage1();
        require 'fixtures/concrete/basic.php';
        $controller = $page->getPageController();
        $this->assertEquals('Concrete\Controller\PageType\Basic', get_class($controller));
        $this->assertInstanceOf('Concrete\Core\Page\Controller\PageTypeController', $controller);
    }

    public function testPageTypeControllerOverride()
    {
        $page = $this->addPage2();

        $root = realpath(DIR_BASE_CORE . '/../application');
        @mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES, 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/alternate.php',
            $root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/alternate.php');

        $controller = $page->getPageController();

        @unlink($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/alternate.php');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);

        $this->assertEquals('Application\Controller\PageType\Alternate', get_class($controller));
    }

    public function testSinglePageController()
    {
        SinglePage::add('/dashboard/reports/forms');
        $reportsPage = Page::getByPath('/dashboard/reports/forms');

        $this->assertInstanceOf('\Concrete\Controller\SinglePage\Dashboard\Reports\Forms', $reportsPage->getPageController());
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\DashboardPageController', $reportsPage->getPageController());
    }

    public function testSinglePageControllerOverride()
    {
        $root = realpath(DIR_BASE_CORE . '/../application');
        if (!is_dir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/dashboard/reports')) {
            mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/dashboard/reports', 0777, true);
        }
        copy(dirname(__FILE__) . '/fixtures/application/forms.php',
        $root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/dashboard/reports/forms.php');

        SinglePage::add('/dashboard/reports/forms');
        $reportsPage = Page::getByPath('/dashboard/reports/forms');
        $controller = $reportsPage->getPageController();

        unlink($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/dashboard/reports/forms.php');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/dashboard/reports');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/dashboard');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS);

        $this->assertEquals('Application\Controller\SinglePage\Dashboard\Reports\Forms', get_class($controller));
        $this->assertInstanceOf('\Concrete\Controller\SinglePage\Dashboard\Reports\Forms', $controller);
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\DashboardPageController', $controller);
    }

    public function testPackagedSinglePageViewPhp()
    {
        $pkg = new Package();
        $pkg->pkgHandle = 'awesome_package';
        $pkg->setPackageID(1);
        $loader = \Concrete\Core\Foundation\ClassLoader::getInstance();
        $loader->registerPackage($pkg);

        $root = realpath(DIR_BASE_CORE . '/../packages');
        @mkdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/package/foo.php',
            $root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/views/foo.php',
            $root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo/view.php');

        SinglePage::add('/testerson/foo', $pkg);
        $fooPage = Page::getByPath('/testerson/foo');
        $fooPage->pkgHandle = 'awesome_package';
        $controller = $fooPage->getPageController();

        @unlink($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @rmdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson');
        @rmdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS);
        @unlink($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo/view.php');
        @rmdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo');
        @rmdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson');
        @rmdir($root . '/awesome_package/' . DIRNAME_PAGES);
        @rmdir($root . '/awesome_package');

        $this->assertEquals('Concrete\Package\AwesomePackage\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }
    public function testPackagedSinglePageViewNoPhp()
    {
        $pkg = new Package();
        $pkg->pkgHandle = 'awesome_package';
        $pkg->setPackageID(1);
        $loader = \Concrete\Core\Foundation\ClassLoader::getInstance();
        $loader->registerPackage($pkg);

        $root = realpath(DIR_BASE_CORE . '/../packages');
        @mkdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/package/foo.php',
            $root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/views/foo.php',
            $root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo.php');

        SinglePage::add('/testerson/foo', $pkg);
        $fooPage = Page::getByPath('/testerson/foo');
        $fooPage->pkgHandle = 'awesome_package';
        $controller = $fooPage->getPageController();

        @unlink($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @rmdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson');
        @rmdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS);
        @unlink($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo.php');
        @rmdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson');
        @rmdir($root . '/awesome_package/' . DIRNAME_PAGES);
        @rmdir($root . '/awesome_package');

        $this->assertEquals('Concrete\Package\AwesomePackage\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }
    public function testApplicableSinglePageViewPhp()
    {
        $root = realpath(DIR_BASE_CORE . '/../application');
        @mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/foo.php',
            $root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/' . DIRNAME_PAGES . '/testerson/foo', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/views/foo.php',
            $root . '/' . DIRNAME_PAGES . '/testerson/foo/view.php');

        SinglePage::add('/testerson/foo');
        $fooPage = Page::getByPath('/testerson/foo');
        $controller = $fooPage->getPageController();

        @unlink($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS);
        @unlink($root . '/' . DIRNAME_PAGES . '/testerson/foo/view.php');
        @rmdir($root . '/' . DIRNAME_PAGES . '/testerson/foo');
        @rmdir($root . '/' . DIRNAME_PAGES . '/testerson');

        $this->assertEquals('Application\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }

    public function testApplicableSinglePageViewNoPhp()
    {
        $root = realpath(DIR_BASE_CORE . '/../application');
        @mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/foo.php',
            $root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/' . DIRNAME_PAGES . '/testerson', 0777, true);
        @copy(dirname(__FILE__) . '/fixtures/application/views/foo.php',
            $root . '/' . DIRNAME_PAGES . '/testerson/foo.php');

        SinglePage::add('/testerson/foo');
        $fooPage = Page::getByPath('/testerson/foo');
        $controller = $fooPage->getPageController();

        @unlink($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson');
        @rmdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS);
        @unlink($root . '/' . DIRNAME_PAGES . '/testerson/foo.php');
        @rmdir($root . '/' . DIRNAME_PAGES . '/testerson');

        $this->assertEquals('Application\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }
}
