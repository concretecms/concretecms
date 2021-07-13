<?php
namespace Concrete\Core\StyleCustomizer\Adapter;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Normalizer\ScssNormalizer;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Processor\ScssProcessor;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class ScssAdapter implements AdapterInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    const FILE_CUSTOMIZABLE_VARIABLES = '_customizable-variables.scss';
    const FILE_ENTRYPOINT = 'main.scss';

    public function getVariablesFile(PresetSkin $skin): string
    {
        return $skin->getDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_SCSS .
            DIRECTORY_SEPARATOR .
            self::FILE_CUSTOMIZABLE_VARIABLES;
    }

    public function getPresetEntryPointFile(PresetSkin $skin): string
    {
        return $skin->getDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_SCSS .
            DIRECTORY_SEPARATOR .
            self::FILE_ENTRYPOINT;
    }

    public function getVariableNormalizer(): NormalizerInterface
    {
        return $this->app->make(ScssNormalizer::class);
    }

    public function getProcessor(): ProcessorInterface
    {
        return $this->app->make(ScssProcessor::class);
    }
}
