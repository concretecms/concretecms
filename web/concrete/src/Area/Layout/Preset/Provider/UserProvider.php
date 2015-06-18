<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\Preset;
use Concrete\Core\Area\Layout\Preset\ProviderInterface;
use Concrete\Core\Area\Layout\Preset\UserPreset;
use Concrete\Core\Page\Page;

class UserProvider implements ProviderInterface
{

    public function getPresets(Page $page)
    {
        $list = UserPreset::getList();
        $presets = array();
        foreach($list as $preset) {
            $p = new Preset($preset->getAreaLayoutPresetName(), $preset->getAreaLayoutObject()->getAreaLayoutColumns());
            $presets[] = $p;
        }
        return $presets;
    }

    public function getName()
    {
        return t('Saved Presets');
    }

}