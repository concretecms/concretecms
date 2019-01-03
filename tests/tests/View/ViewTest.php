<?php

namespace Concrete\Tests\View;

use Concrete\Controller\Install;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
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


}
