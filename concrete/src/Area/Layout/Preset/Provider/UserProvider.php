<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\UserPreset;

class UserProvider implements ProviderInterface
{
    public function getPresets()
    {
        $list = UserPreset::getList();
        $presets = array();
        foreach ($list as $preset) {
            $p = $preset->getPresetObject();
            $presets[] = $p;
        }

        return $presets;
    }

    public function getName()
    {
        return t('Saved Presets');
    }
}
