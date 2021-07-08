<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\StyleList;

/**
 * Responsible for creating style value lists from a variable collection.
 */
class StyleValueListFactory
{

    /**
     * @param StyleList $styleList
     * @param NormalizedVariableCollection $variableCollection
     * @return StyleValueList
     */
    public function createFromVariableCollection(StyleList $styleList, NormalizedVariableCollection $variableCollection): StyleValueList
    {
        $valueList = new StyleValueList();
        foreach ($styleList->getAllStyles() as $style) {
            $value = $style->createValueFromVariableCollection($variableCollection);
            if ($value) {
                $valueList->addValue($style, $value);
            }
        }
        return $valueList;
    }

    /**
     * @param StyleList $styleList
     * @param array $styles
     * @return StyleValueList
     */
    public function createFromRequestArray(StyleList $styleList, array $styles): StyleValueList
    {
        $valueList = new StyleValueList();
        foreach ($styleList->getAllStyles() as $style) {
            $value = $style->createValueFromRequestDataCollection($styles);
            if ($value) {
                $valueList->addValue($style, $value);
            }
        }
        return $valueList;
    }
}
