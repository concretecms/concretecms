<?php

namespace Concrete\TestHelpers\Area;

use Concrete\Core\Area\Layout\Preset\Preset;
use Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface;

class TestAreaLayoutPresetProvider implements ProviderInterface
{
    public function getPresets()
    {
        $formatter = new TestAreaLayoutPresetFormatter();
        $preset = new Preset(
            'preset-1',
            'Preset 1',
            $formatter,
            [
                new HtmlColumn('col-sm-4'),
                new HtmlColumn('col-sm-8'),
            ]);

        return [$preset];
    }

    public function getName()
    {
        return 'Test';
    }
}
