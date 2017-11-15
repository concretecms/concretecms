<?php

namespace Concrete\TestHelpers\Area;

use Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface;

class BrokenTestAreaLayoutPresetProvider implements ProviderInterface
{
    public function getPresets()
    {
        return [1, 2, 3];
    }

    public function getName()
    {
        return 'Test Broken';
    }
}
