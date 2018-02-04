<?php

namespace Concrete\Tests\Area;

use Concrete\Core\Area\Layout\Preset\Preset;
use Concrete\TestHelpers\Area\BrokenTestAreaLayoutPresetProvider;
use Concrete\TestHelpers\Area\TestAreaLayoutPresetProvider;
use Concrete\TestHelpers\Area\TestThemeClass;
use Concrete\TestHelpers\Page\PageTestCase;
use Core;
use Page;
use Request;

class AreaLayoutPresetTest extends PageTestCase
{
    public function __construct()
    {
        $this->tables = array_merge($this->tables, [
            'AreaLayoutPresets',
            'AreaLayoutsUsingPresets',
            'AreaLayouts',
            'AreaLayoutColumns',
            'AreaLayoutCustomColumns',
            'AreaLayoutThemeGridColumns',
        ]);
    }

    public function setUp()
    {
        $service = Core::make('site/type');
        if (!$service->getDefault()) {
            $service->installDefault();
        }

        $service = Core::make('site');
        if (!$service->getDefault()) {
            $service->installDefault('en_US');
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->truncateTables();
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
        $this->assertEquals('<div class="ccm-layout-column" id="ccm-layout-column-4"><div class="ccm-layout-column-inner"></div></div>',
            (string) $columns[0]->getColumnHtmlObject());
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

        $c = Page::addHomePage();
        $c->setTheme($elemental);

        $c = Page::getByID($c->getCollectionID());

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $presets = $manager->getPresets();
        $this->assertCount(1, $presets);
        $c = Page::getCurrentPage();
        $this->assertEquals('Custom Preset', $presets[0]->getName());
        $this->assertInstanceOf(Preset::class, $presets[0]);

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
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
            (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
            (string) $columns[1]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
            (string) $columns[2]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
            (string) $columns[3]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
            (string) $columns[4]->getColumnHtmlObject());
        $this->assertEquals('<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 visible-lg"></div>',
            (string) $columns[5]->getColumnHtmlObject());

        $custom = $presets[3];
        $formatter = $custom->getFormatter();
        $this->assertInstanceOf('\Concrete\Core\Area\Layout\Preset\Formatter\ThemeFormatter', $formatter);
        $this->assertEquals('<div class="row" data-testing="top-row"></div>',
            (string) $formatter->getPresetContainerHtmlObject());

        $columns = $custom->getColumns();
        $this->assertEquals(2, count($columns));
        $this->assertEquals('<div data-foo="foo" class="col-md-2 col-sm-3"></div>',
            (string) $columns[0]->getColumnHtmlObject());
        $this->assertEquals('<div data-bar="bar" class="col-md-10 col-sm-9"></div>',
            (string) $columns[1]->getColumnHtmlObject());

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
        $c = Page::addHomePage();
        $c->setTheme($elemental);

        $c = Page::getByID($c->getCollectionID());

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
