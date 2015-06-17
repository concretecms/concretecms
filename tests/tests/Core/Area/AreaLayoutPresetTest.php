<?php

class TestAreaLayoutPresetProvider implements \Concrete\Core\Area\Layout\Preset\ProviderInterface
{
    public function getPresets(\Concrete\Core\Page\Page $page)
    {

        $preset = new \Concrete\Core\Area\Layout\Preset\Preset('Preset 1', array(
           id(new \HtmlObject\Element('div'))->class('col-sm-4'),
           id(new \HtmlObject\Element('div'))->class('col-sm-8')
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

    protected $tables = array('AreaLayoutPresets');
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

    public function testSavedCustomPresets()
    {

    }

    public function testSavedGridPresets()
    {

    }

}
