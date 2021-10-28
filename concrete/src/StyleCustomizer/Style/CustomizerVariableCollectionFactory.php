<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\ValueContainerInterface;

/**
 * Responsible for looking at a style list and turning that list into a collection of variables fed to the customizer
 * Why do we need this class? Because some types of controls like TypeStyle can have sub controls, but we want to know
 * about their variables at a high level.
 */
class CustomizerVariableCollectionFactory
{

    public function createFromStyleValueList(StyleValueList $valueList): CustomizerVariableCollection
    {
        $collection = new CustomizerVariableCollection();
        foreach ($valueList->getValues() as $styleValue) {
            $style = $styleValue->getStyle();
            $value = $styleValue->getValue();
            if ($value instanceof ValueContainerInterface) {
                // We do NOT include the container (e.g. "type") instead we only include the child sub values
                foreach ($value->getSubStyleValues() as $subStyleValue) {
                    $subStyle = $subStyleValue->getStyle();
                    $subValue = $subStyleValue->getValue();
                    $collection->add(new CustomizerVariable($subStyle->getVariable(), $subValue));
                }
            } else {
                $collection->add(new CustomizerVariable($style->getVariable(), $value));
            }
        }

        return $collection;
    }


}
