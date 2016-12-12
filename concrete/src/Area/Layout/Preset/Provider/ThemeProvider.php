<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\Column;
use Concrete\Core\Area\Layout\Preset\Formatter\ThemeFormatter;
use Concrete\Core\Area\Layout\Preset\Preset;
use Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface;
use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;
use Concrete\Core\Page\Page;

class ThemeProvider implements ProviderInterface
{

    protected $presets = array();
    protected $themeHandle;

    public function __construct(ThemeProviderInterface $interface)
    {
        $arrayPresets = $interface->getThemeAreaLayoutPresets();
        $this->name = $interface->getThemeName();
        $this->themeHandle = $interface->getThemeHandle();

        foreach($arrayPresets as $arrayPreset) {
            $columns = array();
            foreach($arrayPreset['columns'] as $html) {
                $columns[] = Column::fromHtml($html);
            }

            $formatter = new ThemeFormatter($arrayPreset);
            $this->presets[] = new Preset(
                sprintf('theme_%s_%s', $this->themeHandle, $arrayPreset['handle']),
                $arrayPreset['name'],
                $formatter,
                $columns
            );
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPresets()
    {
        return $this->presets;
    }


}