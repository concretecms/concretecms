<?php
namespace Concrete\Core\StyleCustomizer\Adapter;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class LessAdapter implements AdapterInterface
{

    public function getVariablesFile(PresetSkin $skin): string
    {
        throw new \Exception('Not implemented yet.');
    }

    public function getVariableNormalizer(): NormalizerInterface
    {
        throw new \Exception('Not implemented yet.');
    }

    public function getProcessor(): ProcessorInterface
    {
        throw new \Exception('Not implemented yet.');
    }

    public function getPresetEntryPointFile(PresetSkin $skin): string
    {
        throw new \Exception('Not implemented yet.');
    }

}
