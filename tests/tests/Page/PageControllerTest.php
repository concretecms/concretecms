<?php

namespace Concrete\Tests\Page;

use Concrete\Core\Entity\Package;
use Concrete\TestHelpers\Page\PageTestCase;
use Illuminate\Filesystem\Filesystem;
use Page;
use PageTemplate;
use PageType;
use SinglePage;
use Site;

class PageControllerTest extends PageTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->metadatas[] = Package::class;
    }

    public function tearDown()
    {
        parent::tearDown();
        $cache = \Core::make('cache/overrides')->flush();
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
        require DIR_TESTS . '/assets/Page/concrete/basic.php';
        $controller = $page->getPageController();
        $this->assertEquals('Concrete\Controller\PageType\Basic', get_class($controller));
        $this->assertInstanceOf('Concrete\Core\Page\Controller\PageTypeController', $controller);
    }

    public function testPageTypeControllerOverride()
    {
        $page = $this->addPage2();

        $root = realpath(DIR_BASE . '/application');
        @mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES, 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/alternate.php',
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
        copy(DIR_TESTS . '/assets/Page/application/forms.php',
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
        $p = new Package();
        $p->setPackageHandle('awesome_package');
        require_once DIR_TESTS . '/assets/Page/package/awesome_package.php';

        $pkg = new \Concrete\Package\AwesomePackage\Controller(\Core::make('app'));
        $pkg->setPackageEntity($p);

        $pkg->pkgHandle = 'awesome_package';
        $loader = \Concrete\Core\Foundation\ClassLoader::getInstance();
        $loader->registerPackage($pkg);

        $root = realpath(DIR_BASE_CORE . '/../packages');
        @mkdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        copy(DIR_TESTS . '/assets/Page/package/foo.php',
            $root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo', 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/views/foo.php',
            $root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo/view.php');

        $p->setPackageID(1);
        SinglePage::add('/testerson/foo', $p);
        $fooPage = Page::getByPath('/testerson/foo');
        $fooPage->pkgHandle = 'awesome_package';
        $controller = $fooPage->getPageController();

        $fs = new Filesystem();
        $fs->deleteDirectory($root . '/awesome_package/');

        $this->assertEquals('Concrete\Package\AwesomePackage\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }

    public function testPackagedSinglePageViewNoPhp()
    {
        $pkg = new Package();
        $pkg->setPackageHandle('awesome_package');
        $pkg->setPackageID(1);
        $loader = \Concrete\Core\Foundation\ClassLoader::getInstance();
        $loader->registerPackage($pkg);

        $root = realpath(DIR_BASE_CORE . '/../packages');
        @mkdir($root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        copy(DIR_TESTS . '/assets/Page/package/foo.php',
            $root . '/awesome_package/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/awesome_package/' . DIRNAME_PAGES . '/testerson', 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/views/foo.php',
            $root . '/awesome_package/' . DIRNAME_PAGES . '/testerson/foo.php');

        SinglePage::add('/testerson/foo', $pkg);
        $fooPage = Page::getByPath('/testerson/foo');
        $fooPage->pkgHandle = 'awesome_package';
        $controller = $fooPage->getPageController();
        $fooPage->delete();

        $fs = new Filesystem();
        $fs->deleteDirectory($root . '/awesome_package/');

        $this->assertEquals('Concrete\Package\AwesomePackage\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }

    public function testApplicableSinglePageViewPhp()
    {
        $root = realpath(DIR_BASE_CORE . '/../application');
        @mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/foo.php',
            $root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/' . DIRNAME_PAGES . '/testerson/foo', 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/views/foo.php',
            $root . '/' . DIRNAME_PAGES . '/testerson/foo/view.php');

        $failed = false;
        try {
            SinglePage::add('/testerson/foo');
            $fooPage = Page::getByPath('/testerson/foo');
            $controller = $fooPage->getPageController();
            $fooPage->delete();
        } catch (\Exception $e) {
            $failed = $e->getMessage();
        }

        $fs = new Filesystem();
        $fs->deleteDirectory($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson');
        $fs->deleteDirectory($root . '/' . DIRNAME_PAGES . '/testerson');

        if ($failed) {
            $this->fail($failed);
        }
        $this->assertEquals('Application\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }

    public function testApplicableSinglePageViewNoPhp()
    {
        $root = realpath(DIR_BASE_CORE . '/../application');
        @mkdir($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson', 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/foo.php',
            $root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson/foo.php');
        @mkdir($root . '/' . DIRNAME_PAGES . '/testerson', 0777, true);
        copy(DIR_TESTS . '/assets/Page/application/views/foo.php',
            $root . '/' . DIRNAME_PAGES . '/testerson/foo.php');

        $failed = false;
        try {
            SinglePage::add('/testerson/foo');
            $fooPage = Page::getByPath('/testerson/foo');
            $controller = $fooPage->getPageController();
        } catch (\Exception $e) {
            $failed = $e->getMessage();
        }

        $fs = new Filesystem();
        $fs->deleteDirectory($root . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . '/testerson');
        $fs->deleteDirectory($root . '/' . DIRNAME_PAGES . '/testerson');

        if ($failed) {
            $this->fail($failed);
        }

        $this->assertEquals('Application\Controller\SinglePage\Testerson\Foo', get_class($controller));
        $this->assertInstanceOf('\Concrete\Core\Page\Controller\PageController', $controller);
    }

    protected function addPage1()
    {
        $home = Site::getDefault()->getSiteHomePageObject();
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, [
            'uID' => 1,
            'cName' => 'Test page',
            'pTemplateID' => $template->getPageTemplateID(),
        ]);

        return $page;
    }

    protected function addPage2()
    {
        $home = Site::getDefault()->getSiteHomePageObject();
        PageType::add([
            'handle' => 'alternate',
            'name' => 'Alternate',
        ]);

        $pt = PageType::getByHandle('alternate');
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, [
            'uID' => 1,
            'cName' => 'Test page',
            'pTemplateID' => $template->getPageTemplateID(),
        ]);

        return $page;
    }
}
