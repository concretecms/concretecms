<?php

namespace Concrete\Tests\View;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\View;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Concrete\Tests\TestCase;

class SkinnableViewTest extends ConcreteDatabaseTestCase
{

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        Theme::add('elemental');
    }

    protected $tables = [
        'PageThemes',
    ];

    public function testSkinnableLoginView()
    {
        $view = new View('/oauth/authorize');

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

}
