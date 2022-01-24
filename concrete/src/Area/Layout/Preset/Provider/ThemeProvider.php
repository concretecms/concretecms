<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\Column;
use Concrete\Core\Area\Layout\Preset\Formatter\ThemeFormatter;
use Concrete\Core\Area\Layout\Preset\Preset;

class ThemeProvider implements ProviderInterface
{
    /**
     * The name of this provider.
     *
     * @var string
     */
    protected $name;

    /**
     * The handle of the theme
     *
     * @var string
     */
    protected $themeHandle;

    /**
     * The theme preset provider.
     *
     * @var \Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * The available layout presets.
     *
     * @var \Concrete\Core\Area\Layout\Preset\PresetInterface[]
     */
    protected $presets = [];

    public function __construct(ThemeProviderInterface $interface)
    {
        $arrayPresets = $interface->getThemeAreaLayoutPresets();
        $this->name = $interface->getThemeName();
        $this->themeHandle = (string) $interface->getThemeHandle();
        foreach ($arrayPresets as $arrayPreset) {
            $columns = [];
            foreach ($arrayPreset['columns'] as $html) {
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface::getPresets()
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Get the handle of the theme.
     */
    public function getThemeHandle(): string
    {
        return $this->themeHandle;
    }

    /**
     * Get the theme preset provider.
     */
    public function getThemeProvider(): ThemeProviderInterface
    {
        return $this->themeProvider;
    }
}
