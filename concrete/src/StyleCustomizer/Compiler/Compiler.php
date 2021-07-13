<?php
namespace Concrete\Core\StyleCustomizer\Compiler;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Adapter\AdapterInterface;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class Compiler
{

    /**
     * @param AdapterInterface $adapter
     * @param PresetSkin $presetSkin
     * @param NormalizedVariableCollection $collection
     * @return string
     */
    public function compileFromPreset(AdapterInterface $adapter, PresetSkin $presetSkin, NormalizedVariableCollection $collection): string
    {
        $processor = $adapter->getProcessor();
        $file = $adapter->getPresetEntryPointFile($presetSkin);
        $css = $processor->compileFileToString($file, $collection);
        return $css;
    }


}
