<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\ProviderInterface;
use Concrete\Core\Area\Layout\Preset\UserPreset;
use Concrete\Core\Page\Page;

class UserProvider implements ProviderInterface
{

    public function getPresets(Page $page)
    {
        $presets = UserPreset::getList();

    }

    public function getName()
    {
        return t('Saved Presets');
    }

}