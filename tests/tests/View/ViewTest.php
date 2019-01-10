<?php

namespace Concrete\Tests\View;

use Concrete\Controller\Install;
use Concrete\Core\Application\Application;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\DialogView;
use Concrete\Core\View\View;
use Mockery;
use PHPUnit_Framework_TestCase;
use Concrete\Core\Page\Page;

class ViewTest extends PHPUnit_Framework_TestCase
{

    public function testRenderingInstallationView()
    {
        $base = DIR_BASE_CORE;
        $install = new Install();
        $view = $install->getViewObject();
        /**
         * @var $view View
         */
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('concrete', $view->getThemeHandle());
        $this->assertEquals($base . '/views/frontend/install.php', $inner);
        $this->assertEquals($base . '/themes/concrete/view.php', $template);
    }

    public function testRenderMaintenanceMode()
    {
        $base = DIR_BASE_CORE;
        $view = new View('/frontend/maintenance_mode');
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('concrete', $view->getThemeHandle());
        $this->assertEquals($base . '/views/frontend/maintenance_mode.php', $inner);
        $this->assertEquals($base . '/themes/concrete/view.php', $template);
    }

    public function testSkinnableLoginView()
    {
        $view = new View('/oauth/authorize');
        $view->setViewTheme('concrete');
        $view->setViewTemplate('background_image.php');
        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('concrete', $view->getThemeHandle());
        $this->assertEquals(DIR_BASE_CORE . '/views/oauth/authorize.php', $inner);
        $this->assertEquals(DIR_BASE_CORE . '/themes/concrete/background_image.php', $template);

        $app = Facade::getFacadeApplication();
        $collection = $app->make(ThemeRouteCollection::class);
        $collection->setThemeByRoute('/oauth/authorize', 'elemental', 'view.php');

        $view->setupRender();
        $inner = $view->getInnerContentFile();
        $template = $view->getViewTemplate();
        $this->assertEquals('elemental', $view->getThemeHandle());
        $this->assertEquals(DIR_BASE_CORE . '/views/oauth/authorize.php', $inner);
        $this->assertEquals(DIR_BASE_CORE . '/themes/elemental/view.php', $template);

        $collection->setThemesByRoutes($app->make('config')->get('app.theme_paths'));
    }

    public function testLegacyToolsUrlDoesNotMatchDashboardTheme()
    {
        $view = new DialogView('/dashboard/get_image_data');
        $view->setViewRootDirectoryName(DIRNAME_TOOLS);
        $view->setupRender();
        $template = $view->getViewTemplate();
        $file = $view->getInnerContentFile();
        $this->assertNull($template);
        $this->assertEquals(DIR_BASE_CORE . '/tools/dashboard/get_image_data.php', $file);
    }

}
