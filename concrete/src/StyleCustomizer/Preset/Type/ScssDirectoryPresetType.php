<?php

namespace Concrete\Core\StyleCustomizer\Preset\Type;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Preset\Preset;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\Utility\Service\Text;

class ScssDirectoryPresetType implements TypeInterface
{

    const FILE_CUSTOMIZABLE_VARIABLES = '_customizable-variables.scss';
    const FILE_ENTRY_POINT = 'main.scss';

    /**
     * @var Text
     */
    protected $textService;

    /**
     * DirectorySkinType constructor.
     * @param Text $textService
     */
    public function __construct(Text $textService)
    {
        $this->textService = $textService;
    }

    public function createPresetFromPath(string $path, Theme $theme): PresetInterface
    {
        $directoryName = basename($path);
        $skin = new Preset($directoryName, $this->textService->unhandle($directoryName), $theme);
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
            $preset->getIdentifier() .
            DIRECTORY_SEPARATOR .
            self::FILE_CUSTOMIZABLE_VARIABLES;
    }

    public function getEntryPointFile(PresetInterface $preset): string
    {
        return $preset->getTheme()->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_CSS .
            DIRECTORY_SEPARATOR .
            DIRNAME_STYLE_CUSTOMIZER_PRESETS .
            DIRECTORY_SEPARATOR .
            $preset->getIdentifier() .
            DIRECTORY_SEPARATOR .
            self::FILE_ENTRY_POINT;
    }


}
