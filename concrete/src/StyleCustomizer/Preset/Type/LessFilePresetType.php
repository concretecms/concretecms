<?php

namespace Concrete\Core\StyleCustomizer\Preset\Type;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Preset\Preset;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class LessFilePresetType implements TypeInterface
{

    const PRESET_RULE_NAME = '@preset-name';

    /**
     * @var \Less_Parser
     */
    protected $lessParser;

    /**
     * LessFileSkinType constructor.
     * @param \Less_Parser $lessParser
     */
    public function __construct(\Less_Parser $lessParser)
    {
        $this->lessParser = $lessParser;
    }

    protected function getSkinNameFromFile(string $path): string
    {
        $parser = $this->lessParser->parseFile($path, '', true);
        $rules = $parser->rules;
        foreach ($rules as $rule) {
            if (isset($rule->name) && $rule->name == self::PRESET_RULE_NAME) {
                return $rule->value->value[0]->value[0]->value;
            }
        }
        return '';
    }

    public function createPresetFromPath(string $path, Theme $theme): PresetInterface
    {
        $filename = basename($path);
        $identifier = substr($filename, 0, strpos($filename, '.less'));
        $skin = new Preset($identifier, $this->getSkinNameFromFile($path), $theme);
        return $skin;
    }

    public function getVariablesFile(PresetInterface $preset): string
    {
        return $preset->getTheme()->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_CSS .
            DIRECTORY_SEPARATOR .
            DIRNAME_STYLE_CUSTOMIZER_PRESETS .
            DIRECTORY_SEPARATOR .
            $preset->getIdentifier() . '.less';
    }

    public function getEntryPointFile(PresetInterface $preset): string
    {
        return $preset->getTheme()->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_CSS .
            DIRECTORY_SEPARATOR .
            'main.less';
    }

}
