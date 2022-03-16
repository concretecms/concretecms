<?php
namespace Concrete\Core\StyleCustomizer\Compiler;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Customizer\Type\TypeInterface;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;

class Compiler
{

    /**
     * @param Customizer $customizer
     * @param PresetInterface $preset
     * @param NormalizedVariableCollection $collection
     * @return string
     */
    public function compileFromPreset(Customizer $customizer, PresetInterface $preset, NormalizedVariableCollection $collection): string
    {
        $customizerType = $customizer->getType();
        $presetType = $customizerType->getPresetType();
        $processor = $customizerType->getStyleProcessor();
        $file = $presetType->getEntryPointFile($preset);
        $css = $processor->compileFileToString($file, $collection);
        return $css;
    }

    public function compileFromCustomSkin(Customizer $customizer, CustomSkin $skin, NormalizedVariableCollection $collection): string
    {
        $preset = $customizer->getPresetByIdentifier($skin->getPresetStartingPoint());
        return $this->compileFromPreset($customizer, $preset, $collection);
    }
}
