<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Concrete\Core\StyleCustomizer\Adapter\AdapterInterface;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\StyleValueList;

/**
 * Responsible for creating a normalized collection of LESS/SCSS variables from a variety of input sources
 */
class NormalizedVariableCollectionFactory
{

    public function createFromStyleValueList(StyleValueList $valueList): NormalizedVariableCollection
    {
        $collection = new NormalizedVariableCollection();
        foreach ($valueList->getValues() as $styleValue) {
            $style = $styleValue->getStyle();
            $value = $styleValue->getValue();
            $variable = $style->createVariableFromValue($value);
            if ($variable) {
                $collection->add($variable);
            }
        }

        return $collection;
    }

    public function createVariableCollectionFromSkin(AdapterInterface $adapter, SkinInterface $skin): NormalizedVariableCollection
    {
        $file = $adapter->getVariablesFile($skin);
        $normalizer = $adapter->getVariableNormalizer();
        return $normalizer->createVariableCollectionFromFile($file);
    }
}
