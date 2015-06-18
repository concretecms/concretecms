<?php

class HtmlColumn implements \Concrete\Core\Area\Layout\ColumnInterface
{

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function getColumnHtmlObject()
    {
        $column = new \HtmlObject\Element('div');
        $column->addClass($this->class);
        return $column;
    }

}

class TestAreaLayoutPresetProvider implements \Concrete\Core\Area\Layout\Preset\ProviderInterface
{
    public function getPresets(\Concrete\Core\Page\Page $page)
    {

        $preset = new \Concrete\Core\Area\Layout\Preset\Preset('Preset 1', array(
           new HtmlColumn('col-sm-4'),
            new HtmlColumn('col-sm-8')
        ));
        return array($preset);
    }

    public function getName()
    {
        return 'Test';
    }
}

class BrokenTestAreaLayoutPresetProvider implements \Concrete\Core\Area\Layout\Preset\ProviderInterface
{
    public function getPresets(\Concrete\Core\Page\Page $page)
    {
        return array(1,2,3);
    }

    public function getName()
    {
        return 'Test Broken';
    }
}


class AreaLayoutPresetTest extends ConcreteDatabaseTestCase
{

    protected $tables = array('AreaLayoutPresets','AreaLayouts','AreaLayoutColumns',
        'AreaLayoutCustomColumns', 'AreaLayoutThemeGridColumns', 'PageThemes', 'Pages', 'Collections',
        'CollectionVersions', 'PagePaths');
    protected $fixtures = array();

    public function testPresetProviderManagerRetrievePresets()
    {
        /** @var $manager \Concrete\Core\Area\Layout\Preset\ProviderManager */
        $manager = Core::make('manager/area_layout_preset_provider');
        $broken = new BrokenTestAreaLayoutPresetProvider();
        $manager->register(new TestAreaLayoutPresetProvider());
        $manager->register($broken);

        $this->assertEquals(3, count($manager->getProviders()));

        $manager->unregister('Test');
        $this->assertEquals(2, count($manager->getProviders()));

        $manager->unregister($broken);
        $this->assertEquals(1, count($manager->getProviders()));

        $c = new Page();
        $presets = $manager->getPresets($c);
        $this->assertEquals(0, count($presets));

        $manager->register(new TestAreaLayoutPresetProvider());
        $presets = $manager->getPresets($c);
        $this->assertEquals(1, count($presets));
        $manager->unregister('Test');

        $columns = $presets[0]->getColumns();
        $this->assertEquals(2, count($columns));
        $this->assertEquals('<div class="col-sm-4"></div>', (string) $columns[0]->getColumnHtmlObject());
    }

    public function testSavedCustomPresets()
    {
        $layout = \Concrete\Core\Area\Layout\CustomLayout::add(20, false);
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $preset1 = \Concrete\Core\Area\Layout\Preset\UserPreset::add($layout, 'Custom Preset');

        $layout = \Concrete\Core\Area\Layout\CustomLayout::add(10, false);
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $preset2 = \Concrete\Core\Area\Layout\Preset\UserPreset::add($layout, 'Custom Preset 2');

        /** @var $manager \Concrete\Core\Area\Layout\Preset\ProviderManager */
        $manager = Core::make('manager/area_layout_preset_provider');
        $c = new Page();
        $presets = $manager->getPresets($c);
        $this->assertEquals(2, count($presets));
        $this->assertEquals('Custom Preset 2', $presets[1]->getName());
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Preset', $presets[1]);

        $columns = $presets[1]->getColumns();
        $this->assertEquals('<div class="ccm-layout-column"></div>', (string) $columns[0]->getColumnHtmlObject());


    }

    public function testSavedGridPresets()
    {
        $layout = \Concrete\Core\Area\Layout\ThemeGridLayout::add();
        $layout->addLayoutColumn()->setAreaLayoutColumnSpan(4);
        $column = $layout->addLayoutColumn();
        $column->setAreaLayoutColumnSpan(2);
        $column->setAreaLayoutColumnOffset(2);
        $layout->addLayoutColumn()->setAreaLayoutColumnSpan(6);
        $preset1 = \Concrete\Core\Area\Layout\Preset\UserPreset::add($layout, 'Custom Preset');

        /** @var $manager \Concrete\Core\Area\Layout\Preset\ProviderManager */
        $manager = Core::make('manager/area_layout_preset_provider');


        $elemental = \Concrete\Core\Page\Theme\Theme::add('elemental');
        Page::addHomePage();
        Core::make('cache/request')->disable();
        $c = Page::getByID(1);
        $c->setTheme($elemental);

        $c = Page::getByID(1);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $presets = $manager->getPresets($c);
        $this->assertEquals(1, count($presets));
        $c = Page::getCurrentPage();
        $this->assertEquals('Custom Preset', $presets[0]->getName());
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Preset', $presets[0]);

        $columns = $presets[0]->getColumns();
        $this->assertEquals('<div class="col-sm-4"></div>', (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-sm-2 col-sm-offset-2"></div>', (string) $columns[1]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-sm-6"></div>', (string) $columns[2]->getColumnHtmlObject());
    }

    /**
     * @expectedException \Concrete\Core\Area\Layout\Preset\InvalidPresetException
     */
    public function testBrokenRetrievePresets()
    {
        /** @var $manager \Concrete\Core\Area\Layout\Preset\ProviderManager */
        $manager = Core::make('manager/area_layout_preset_provider');
        $broken = new BrokenTestAreaLayoutPresetProvider();
        $manager->register($broken);
        $c = new Page();
        $presets = $manager->getPresets($c);
        $this->assertEquals(3, count($presets));
    }



}
