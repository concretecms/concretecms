<?php

namespace Concrete\Tests\View;

use Concrete\Controller\Install;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Single;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\View\View;
use Concrete\TestHelpers\Page\PageTestCase;
use Mockery;
use PHPUnit_Framework_TestCase;
use Concrete\Core\Page\Page;

class PageViewTest extends PageTestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Theme::add('elemental');
    }

    public function testRenderingPage()
    {
        $elemental = Theme::getByHandle('elemental');
        $twoColumn = Template::add('right_sidebar', 'Right Sidebar');

        $base = DIR_BASE_CORE;
        $about = self::createPage('About');
        $about->setTheme($elemental);
        $controller = $about->getPageController();

        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('elemental', $view->getThemeHandle());
        $this->assertEquals(null, $inner);
        $this->assertEquals($base . '/themes/elemental/full.php', $template);

        $another = self::createPage('About', false, false, $twoColumn);
        $another->setTheme($elemental);

        $controller = $another->getPageController();
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('elemental', $view->getThemeHandle());
        $this->assertEquals(null, $inner);
        $this->assertEquals($base . '/themes/elemental/right_sidebar.php', $template);
    }

    public function testRenderingDashboardPage()
    {
        $base = DIR_BASE_CORE;
        $sitemap = Single::add('/dashboard/sitemap');
        $controller = $sitemap->getPageController();

        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals($inner, $base . '/single_pages/dashboard/sitemap/view.php');
        $this->assertEquals($base . '/themes/dashboard/view.php', $template);
        $this->assertEquals('dashboard', $view->getThemeHandle());
    }

    public function testRenderingDashboardPageWithCustomThemeViewTemplate()
    {
        $base = DIR_BASE_CORE;
        $addons = Single::add('/dashboard/extend/addons');
        $controller = $addons->getPageController();
        // Ideally I wouldn't have to duplicate this logic here since it's in the controller but
        // there is too much there that tries to run if I just run the marketplace controller's view() method.
        $controller->setThemeViewTemplate('marketplace.php');
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals($base . '/single_pages/dashboard/extend/addons.php', $inner);
        $this->assertEquals($base . '/themes/dashboard/marketplace.php', $template);
        $this->assertEquals('dashboard', $view->getThemeHandle());
    }

    public function testRenderingEditProfilePage()
    {
        $edit_profile = Single::add('/account/edit_profile');
        $controller = $edit_profile->getPageController();
        $controller->setThemeViewTemplate('account.php');
        $controller->setTheme('concrete');
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('concrete', $view->getThemeHandle());
        $this->assertEquals(DIR_BASE_CORE . '/single_pages/account/edit_profile.php', $inner);
        $this->assertEquals(DIR_BASE_CORE . '/themes/concrete/account.php', $template);
    }

    public function testRenderingEditProfilePageWithDashboardOverride()
    {
        $edit_profile = Single::add('/account/edit_profile');
        $controller = $edit_profile->getPageController();
        $controller->setTheme('dashboard');
        $controller->setThemeViewTemplate('account.php'); // This should be ignored because the dashboard doesn't have this.
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('dashboard', $view->getThemeHandle());
        $this->assertEquals(DIR_BASE_CORE . '/single_pages/account/edit_profile.php', $inner);
        $this->assertEquals(DIR_BASE_CORE . '/themes/dashboard/view.php', $template);
    }

    public function testRenderingEditProfilePageThemePathOverride()
    {
        $collection = $this->app->make(ThemeRouteCollection::class);
        $collection->setThemeByRoute('/account/*', 'elemental', 'full.php');

        $edit_profile = Single::add('/account/edit_profile');
        $controller = $edit_profile->getPageController();
        $controller->setTheme('dashboard');
        $controller->setThemeViewTemplate('account.php'); // This should be ignored because the dashboard doesn't have this.
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('elemental', $view->getThemeHandle());
        $this->assertEquals(DIR_BASE_CORE . '/single_pages/account/edit_profile.php', $inner);
        $this->assertEquals(DIR_BASE_CORE . '/themes/elemental/full.php', $template);

        $collection->setThemesByRoutes($this->app->make('config')->get('app.theme_paths'));
    }


    public function testRenderingRegisterWithReplacedNotFound()
    {
        $base = DIR_BASE_CORE;

        $elemental = Theme::getByHandle('elemental');
        $pageNotFound = Single::add('/page_not_found');
        $pageNotFound->setTheme($elemental);

        $register = Single::add('/register');
        $controller = $register->getPageController();
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals($inner, $base . '/single_pages/register.php');
        $this->assertEquals($base . '/themes/concrete/view.php', $template);
        $this->assertEquals('concrete', $view->getThemeHandle());

        $controller->replace('/page_not_found');
        $controller = $controller->getReplacement();
        $this->assertInstanceOf(PageController::class, $controller);

        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals(null, $inner); // because it lives in elemental
        $this->assertEquals($base . '/themes/elemental/page_not_found.php', $template);
        $this->assertEquals('elemental', $view->getThemeHandle());
    }


    public function testThemeRouteCollectionLogin()
    {
        $base = DIR_BASE_CORE;

        $login = Single::add('/login');
        $controller = $login->getPageController();
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals($inner, $base . '/single_pages/login.php');
        $this->assertEquals($base . '/themes/concrete/background_image.php', $template);
        $this->assertEquals('concrete', $view->getThemeHandle());

        $collection = $this->app->make(ThemeRouteCollection::class);
        $collection->setThemeByRoute('/login', 'elemental', 'full.php');

        $login = Page::getByPath('/login');
        $controller = $login->getPageController();
        $view = $controller->getViewObject();
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals($inner, $base . '/single_pages/login.php');
        $this->assertEquals($base . '/themes/elemental/full.php', $template);
        $this->assertEquals('elemental', $view->getThemeHandle());

        $collection->setThemesByRoutes($this->app->make('config')->get('app.theme_paths'));
    }

}
