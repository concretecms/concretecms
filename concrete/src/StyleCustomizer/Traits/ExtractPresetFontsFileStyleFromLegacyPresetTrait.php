<?php
namespace Concrete\Core\StyleCustomizer\Traits;

use Concrete\Core\StyleCustomizer\Customizer\Type\LegacyCustomizerType;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\PresetFontsFileStyle;
use Concrete\Core\StyleCustomizer\Style\StyleValue;
use Concrete\Core\StyleCustomizer\Style\StyleValueList;
use Concrete\Core\StyleCustomizer\Style\Value\PresetFontsFileValue;
use Config;

/**
 * @deprecated
 */
trait ExtractPresetFontsFileStyleFromLegacyPresetTrait
{

    public function addPresetFontsFileStyleToStyleValueList(LegacyCustomizerType $type, PresetInterface $preset, StyleValueList $styleValueList)
    {
        $presetFile = $type->getPresetType()->getVariablesFile($preset);
        $fileVariableCollection = $type->getVariableNormalizer()->createVariableCollectionFromFile($presetFile);
        $presetFontsFileVariable = $fileVariableCollection->getVariable('preset-fonts-file');
        if ($presetFontsFileVariable) {
            $styleValueList->add(new StyleValue(new PresetFontsFileStyle(), new PresetFontsFileValue($presetFontsFileVariable->getValue())));
        }
    }
}
