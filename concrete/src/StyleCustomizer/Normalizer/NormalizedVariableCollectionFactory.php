<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\StyleCustomizer\Adapter\AdapterInterface;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Style\StyleValueList;
use Concrete\Core\StyleCustomizer\Style\Value\ValueContainerInterface;

/**
 * Responsible for creating a normalized collection of LESS/SCSS variables from a variety of input sources
 */
class NormalizedVariableCollectionFactory
{

    protected $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createFromStyleValueList(StyleValueList $valueList): NormalizedVariableCollection
    {
        $collection = new NormalizedVariableCollection();
        foreach ($valueList->getValues() as $styleValue) {
            $style = $styleValue->getStyle();
            $value = $styleValue->getValue();
            if ($value instanceof ValueContainerInterface) {
                foreach ($value->getSubStyleValues() as $subStyleValue) {
                    $subStyle = $subStyleValue->getStyle();
                    $subValue = $subStyleValue->getValue();
                    $variable = $subStyle->createVariableFromValue($subValue);
                    if ($variable) {
                        $collection->add($variable);
                    }
                }
            } else {
                $variable = $style->createVariableFromValue($value);
                if ($variable) {
                    $collection->add($variable);
                }
            }
        }

        return $collection;
    }

    public function createFromPreset(Customizer $customizer, PresetInterface $preset): NormalizedVariableCollection
    {
        $type = $customizer->getType();
        $file = $type->getPresetType()->getVariablesFile($preset);
        $normalizer = $type->getVariableNormalizer();
        return $normalizer->createVariableCollectionFromFile($file);
    }

    public function createFromCustomSkin(CustomSkin $skin)
    {
        $collection = $skin->getVariableCollection();
        if ($collection) {
            return $this->serializer->denormalize($collection, NormalizedVariableCollection::class);
        }
        return new NormalizedVariableCollection();
    }
}
