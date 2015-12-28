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

    public function getColumnHtmlObjectEditMode()
    {
        return $this->getColumnHtmlObject();
    }

}

class TestAreaLayoutPresetFormatter implements \Concrete\Core\Area\Layout\Preset\Formatter\FormatterInterface
{
    public function getPresetContainerHtmlObject()
    {
        $column = new \HtmlObject\Element('div');
        $column->addClass('foo');
        return $column;
    }
}
class TestAreaLayoutPresetProvider implements \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface
{
    public function getPresets()
    {
        $formatter = new TestAreaLayoutPresetFormatter();
        $preset = new \Concrete\Core\Area\Layout\Preset\Preset('preset-1', 'Preset 1',
            $formatter, array(
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

class BrokenTestAreaLayoutPresetProvider implements \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface
{
    public function getPresets()
    {
        return array(1,2,3);
    }

    public function getName()
    {
        return 'Test Broken';
    }
}

class TestThemeClass implements \Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface
{
    public function getThemeHandle()
    {
        return 'test_theme';
    }

    public function getThemeName()
    {
        return 'Test Theme';
    }

    public function getThemeAreaLayoutPresets()
    {
        $presets = array(
            array(
                'handle' => 'left_sidebar',
                'name' => 'Left Sidebar',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-sm-4"></div>',
                    '<div class="col-sm-8"></div>'
                ),
            ),
            array(
                'handle' => 'exciting',
                'name' => 'Exciting',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 visible-lg"></div>',
                ),
            ),
            array(
                'handle' => 'three_column',
                'name' => 'Three Column',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-md-4"></div>',
                    '<div class="col-md-4"></div>',
                    '<div class="col-md-4"></div>',
                ),
            ),
            array(
                'handle' => 'test_layout',
                'name' => 'Test Layout',
                'container' => '<div class="row" data-testing="top-row"></div>',
                'columns' => array(
                    '<div data-foo="foo" class="col-md-2 col-sm-3"></div>',
                    '<div data-bar="bar" class="col-md-10 col-sm-9"></div>'
                ),
            )

        );
        return $presets;
    }
}

class AreaLayoutPresetTest extends PageTestCase
{

    protected function setUp() {
        $this->tables = array_merge($this->tables, array('AreaLayoutPresets','AreaLayoutsUsingPresets', 'AreaLayouts','AreaLayoutColumns',
            'AreaLayoutCustomColumns', 'AreaLayoutThemeGridColumns'));
        parent::setUp();
    }

    public function testPresetProviderManagerRetrievePresets()
    {
        /** @var $manager \Concrete\Core\Area\Layout\Preset\Provider\Manager */
        $manager = Core::make('manager/area_layout_preset_provider');
        $manager->unregister('Active Theme');
        $broken = new BrokenTestAreaLayoutPresetProvider();
        $manager->register(new TestAreaLayoutPresetProvider());
        $manager->register($broken);

        $this->assertEquals(3, count($manager->getProviders()));

        $manager->unregister('Test');
        $this->assertEquals(2, count($manager->getProviders()));

        $manager->unregister($broken);
        $this->assertEquals(1, count($manager->getProviders()));

        $c = new Page();
        $presets = $manager->getPresets();
        $this->assertEquals(0, count($presets));

        $manager->register(new TestAreaLayoutPresetProvider());
        $presets = $manager->getPresets();
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

        /** @var $manager \Concrete\Core\Area\Layout\Preset\Provider\Manager */
        $manager = Core::make('manager/area_layout_preset_provider');
        $c = new Page();
        $presets = $manager->getPresets();
        $this->assertEquals(2, count($presets));
        $this->assertEquals('Custom Preset 2', $presets[1]->getName());
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Preset', $presets[1]);

        $columns = $presets[1]->getColumns();
        $this->assertEquals('<div class="ccm-layout-column" id="ccm-layout-column-4"><div class="ccm-layout-column-inner"></div></div>', (string) $columns[0]->getColumnHtmlObject());

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

        /** @var $manager \Concrete\Core\Area\Layout\Preset\Provider\Manager */
        $manager = Core::make('manager/area_layout_preset_provider');

        $elemental = \Concrete\Core\Page\Theme\Theme::add('elemental');
        Core::make('cache/request')->disable();
        $c = Page::getByID(1);
        $c->setTheme($elemental);

        $c = Page::getByID(1);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $presets = $manager->getPresets();
        $this->assertEquals(1, count($presets));
        $c = Page::getCurrentPage();
        $this->assertEquals('Custom Preset', $presets[0]->getName());
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Preset', $presets[0]);

        $columns = $presets[0]->getColumns();
        $this->assertEquals('<div class="col-sm-4"></div>', (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-sm-2 col-sm-offset-2"></div>', (string) $columns[1]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-sm-6"></div>', (string) $columns[2]->getColumnHtmlObject());

        $req->clearCurrentPage();

    }

    public function testSavePreset()
    {
        $layout = \Concrete\Core\Area\Layout\CustomLayout::add(20, false);
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $preset1 = \Concrete\Core\Area\Layout\Preset\UserPreset::add($layout, 'Custom Preset');

        $layout = \Concrete\Core\Area\Layout\CustomLayout::add(10, false);
        $layout->addLayoutColumn();
        $layout->addLayoutColumn();
        $layoutID = $layout->getAreaLayoutID();
        $preset2 = \Concrete\Core\Area\Layout\Preset\UserPreset::add($layout, 'Custom Preset 2');

        $layout = \Concrete\Core\Area\Layout\PresetLayout::add($preset2->getPresetObject());
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\PresetLayout', $layout);
        $this->assertEquals($layoutID, $layout->getAreaLayoutPresetHandle());
        $this->assertEquals(2, $layout->getAreaLayoutNumColumns());
    }
    public function testThemePresets()
    {
        $manager = Core::make('manager/area_layout_preset_provider');
        $provider = new \Concrete\Core\Area\Layout\Preset\Provider\ThemeProvider(new TestThemeClass());
        $manager->register($provider);
        $c = new Page();
        $presets = $manager->getPresets();
        $this->assertEquals(4, count($presets));
        $best = $presets[1];
        $this->assertEquals('Exciting', $best->getName());

        $formatter = $best->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Formatter\ThemeFormatter', $formatter);
        $this->assertEquals('<div class="row"></div>', (string) $formatter->getPresetContainerHtmlObject());

        $columns = $best->getColumns();
        $this->assertEquals(6, count($columns));
        $this->assertEquals('theme_test_theme_exciting', $best->getIdentifier());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>', (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>', (string) $columns[1]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>', (string) $columns[2]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>', (string) $columns[3]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>', (string) $columns[4]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 visible-lg"></div>', (string) $columns[5]->getColumnHtmlObject());


        $custom = $presets[3];
        $formatter = $custom->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Formatter\ThemeFormatter', $formatter);
        $this->assertEquals('<div class="row" data-testing="top-row"></div>', (string) $formatter->getPresetContainerHtmlObject());

        $columns = $custom->getColumns();
        $this->assertEquals(2, count($columns));
        $this->assertEquals('<div data-foo="foo" class="col-md-2 col-sm-3"></div>', (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div data-bar="bar" class="col-md-10 col-sm-9"></div>', (string) $columns[1]->getColumnHtmlObject());

        $manager->unregister($provider);
    }

    public function testElementalThemePresetsPageWithNoTheme()
    {
        $elemental = \Concrete\Core\Page\Theme\Theme::add('elemental');
        $manager = Core::make('manager/area_layout_preset_provider');
        $c = new Page();
        $presets = $manager->getPresets();
        $this->assertEquals(0, count($presets));
    }

    public function testElementalThemePresetsPageWithTheme()
    {
        $elemental = \Concrete\Core\Page\Theme\Theme::add('elemental');

        Core::make('cache/request')->disable();
        $c = Page::getByID(1);
        $c->setTheme($elemental);

        $c = Page::getByID(1);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $manager = Core::make('manager/area_layout_preset_provider');
        $manager->register(new \Concrete\Core\Area\Layout\Preset\Provider\ActiveThemeProvider());
        $presets = $manager->getPresets();
        $this->assertEquals(2, count($presets));
        $preset = $presets[0];

        $formatter = $preset->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Formatter\ThemeFormatter', $formatter);
        $this->assertEquals('<div class="row"></div>', (string) $formatter->getPresetContainerHtmlObject());

        $this->assertEquals('Left Sidebar', $preset->getName());
        $columns = $preset->getColumns();
        $this->assertEquals(2, count($columns));
        $this->assertEquals('theme_elemental_left_sidebar', $preset->getIdentifier());
        $this->assertEquals('<div class="col-sm-4"></div>', (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-sm-8"></div>', (string) $columns[1]->getColumnHtmlObject());

        $req->clearCurrentPage();
    }


}
