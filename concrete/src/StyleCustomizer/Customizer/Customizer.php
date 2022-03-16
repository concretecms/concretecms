<?php
namespace Concrete\Core\StyleCustomizer\Customizer;

use Concrete\Core\Application\Application;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Preset\PresetFactory;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\StyleList;
use Concrete\Core\StyleCustomizer\StyleListFactory;

final class Customizer
{

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var string
     */
    protected $configurationFile;


    /**
     * @var \Concrete\Core\StyleCustomizer\Customizer\Type\TypeInterface
     */
    protected $type;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var PresetFactory
     */
    protected $presetFactory;

    public function __construct(Application $app, PresetFactory $presetFactory)
    {
        $this->app = $app;
        $this->presetFactory = $presetFactory;
    }

    /**
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return string
     */
    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    /**
     * @param string $configurationFile
     */
    public function setConfigurationFile(string $configurationFile): void
    {
        $this->configurationFile = $configurationFile;
    }

    /**
     * @return Type\TypeInterface
     */
    public function getType(): Type\TypeInterface
    {
        return $this->type;
    }

    /**
     * @param Type\TypeInterface $type
     */
    public function setType(Type\TypeInterface $type): void
    {
        $this->type = $type;
    }

    /**
     * @return PresetInterface[]
     */
    public function getPresets(): array
    {
        $type = $this->getType();
        return $this->presetFactory->createFromTheme($this->getTheme(), $type->getPresetType());
    }

    public function getPresetByIdentifier(string $identifier): ?PresetInterface
    {
        foreach ($this->getPresets() as $preset) {
            if ($preset->getIdentifier() === $identifier) {
                return $preset;
            }
        }
        return null;
    }

    public function supportsCustomSkins(): bool
    {
        return $this->getType()->supportsCustomSkins();
    }

    public function supportsPageCustomization(): bool
    {
        return $this->getType()->supportsPageCustomization();
    }

    public function getThemeCustomizableStyleList(PresetInterface $preset): StyleList
    {
        $xml = simplexml_load_file($this->getConfigurationFile());
        $factory = $this->app->make(StyleListFactory::class);
        return $factory->createStyleList($this->getType()->getParserManager(), $xml, $preset);
    }
}
