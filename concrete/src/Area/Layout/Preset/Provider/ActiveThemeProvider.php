<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Page;

class ActiveThemeProvider implements ProviderInterface
{
    protected $presets = array();

    public function __construct()
    {
        $c = Page::getCurrentPage();
        if (is_object($c)) {
            $theme = $c->getCollectionThemeObject();
            if (is_object($theme)) {
                if ($theme instanceof ThemeProviderInterface) {
                    $provider = new ThemeProvider($theme);
                    $this->presets = $provider->getPresets();
                    $this->name = $provider->getName();
                }
            }
        }
    }

    public function getName()
    {
        return 'Active Theme';
    }

    public function getPresets()
    {
        return $this->presets;
    }
}
