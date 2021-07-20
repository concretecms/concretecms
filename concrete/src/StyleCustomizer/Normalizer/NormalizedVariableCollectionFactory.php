<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\StyleCustomizer\Adapter\AdapterInterface;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
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

    public function createVariableCollectionFromSkin(AdapterInterface $adapter, SkinInterface $skin): NormalizedVariableCollection
    {
        if ($skin instanceof PresetSkin) {
            $file = $adapter->getVariablesFile($skin);
            $normalizer = $adapter->getVariableNormalizer();
            return $normalizer->createVariableCollectionFromFile($file);
        }
        if ($skin instanceof CustomSkin) {
            $collection = $skin->getVariableCollection();
            if ($collection) {
                return $this->serializer->denormalize($collection, NormalizedVariableCollection::class);
            }
        }
        throw new \Exception(t('Unable to create variable collection from skin: %s', $skin->getName()));
    }
}
