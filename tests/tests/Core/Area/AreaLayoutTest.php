<?php


class AreaLayoutTest extends ConcreteDatabaseTestCase
{
    protected $tables = array('AreaLayoutPresets', 'AreaLayouts', 'AreaLayoutColumns',
        'AreaLayoutCustomColumns', 'AreaLayoutThemeGridColumns', 'PageThemes', 'Pages', 'Collections',
        'CollectionVersions', 'PagePaths', );
    protected $fixtures = array();

    public function testCustomAreaLayoutContainer()
    {
        $layout = \Concrete\Core\Area\Layout\CustomLayout::add(20, false);
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();

        $layout = \Concrete\Core\Area\Layout\Layout::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\CustomLayout', $layout);
        $columns = $layout->getAreaLayoutColumns();
        $this->assertEquals(3, count($columns));

        $formatter = $layout->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Formatter\CustomFormatter', $formatter);
        $this->assertEquals('<div class="ccm-layout-column-wrapper" id="ccm-layout-column-wrapper-1"></div>', (string) $formatter->getLayoutContainerHtmlObject());
    }

    public function testThemeGridAreaLayoutContainer()
    {
        $layout = \Concrete\Core\Area\Layout\ThemeGridLayout::add();
        $layout->addLayoutColumn()->setAreaLayoutColumnSpan(4);
        $column = $layout->addLayoutColumn();
        $column->setAreaLayoutColumnSpan(2);
        $column->setAreaLayoutColumnOffset(2);
        $layout->addLayoutColumn()->setAreaLayoutColumnSpan(6);

        $elemental = \Concrete\Core\Page\Theme\Theme::add('elemental');
        Page::addHomePage();
        Core::make('cache/request')->disable();
        $c = Page::getByID(1);
        $c->setTheme($elemental);

        $c = Page::getByID(1);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $layout = \Concrete\Core\Area\Layout\Layout::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\ThemeGridLayout', $layout);
        $columns = $layout->getAreaLayoutColumns();
        $this->assertEquals(3, count($columns));

        $formatter = $layout->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Formatter\ThemeGridFormatter', $formatter);
        $this->assertEquals('<div class="row"></div>', (string) $formatter->getLayoutContainerHtmlObject());

        $req->clearCurrentPage();
    }
}
