<?php

namespace Concrete\Tests\Area;

use Concrete\Core\Area\Layout\Layout;
use Concrete\Core\Area\Layout\ThemeGridLayout;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;
use Page;
use Request;

class AreaLayoutTest extends ConcreteDatabaseTestCase
{
    protected $tables = ['AreaLayoutPresets', 'AreaLayouts', 'AreaLayoutColumns',
        'AreaLayoutCustomColumns', 'AreaLayoutThemeGridColumns', 'PageThemes', 'Pages', 'Collections',
        'CollectionVersions', ];
    protected $fixtures = [];

    protected $metadatas = [
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\Page\PagePath',
    ];

    public function testCustomAreaLayoutContainer()
    {
        $layout = \Concrete\Core\Area\Layout\CustomLayout::add(20, false);
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();

        $layout = Layout::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\CustomLayout', $layout);
        $columns = $layout->getAreaLayoutColumns();
        $this->assertEquals(3, count($columns));

        $formatter = $layout->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Formatter\CustomFormatter', $formatter);
        $this->assertEquals('<div class="ccm-layout-column-wrapper" id="ccm-layout-column-wrapper-1"></div>', (string) $formatter->getLayoutContainerHtmlObject());
    }

    public function testThemeGridAreaLayoutContainer()
    {
        $this->truncateTables();

        $layout = \Concrete\Core\Area\Layout\ThemeGridLayout::add();
        $layout->addLayoutColumn()->setAreaLayoutColumnSpan(4);
        $column = $layout->addLayoutColumn();
        $column->setAreaLayoutColumnSpan(2);
        $column->setAreaLayoutColumnOffset(2);
        $layout->addLayoutColumn()->setAreaLayoutColumnSpan(6);

        $elemental = \Concrete\Core\Page\Theme\Theme::add('elemental');
        $service = \Core::make('site/type');
        if (!$service->getDefault()) {
            $service->installDefault();
        }
        $service = \Core::make('site');
        if (!$service->getDefault()) {
            $service->installDefault();
        }
        Page::addHomePage();
        Core::make('cache/request')->disable();
        $c = Page::getByID(1);
        $c->setTheme($elemental);

        $c = Page::getByID(1);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $layout = Layout::getByID(1);
        $this->assertInstanceOf(ThemeGridLayout::class, $layout);
        $columns = $layout->getAreaLayoutColumns();
        $this->assertCount(3, $columns);

        $formatter = $layout->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Formatter\ThemeGridFormatter', $formatter);
        $this->assertEquals('<div class="row"></div>', (string) $formatter->getLayoutContainerHtmlObject());

        $req->clearCurrentPage();
    }
}
