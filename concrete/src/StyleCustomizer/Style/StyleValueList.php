<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\Value;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Concrete\Core\StyleCustomizer\StyleList;

/**
 * Class StyleValueList
 * Responsible for joining the styles found in a theme's styles.xml file with the actual values they have in a particular
 * context.
 *
 * @package Concrete\Core\StyleCustomizer\Style
 */
class StyleValueList
{

    /**
     * @var StyleValue[]
     */
    protected $values = [];

    public function addValue(StyleInterface $style, ValueInterface $value)
    {
        $this->values[] = new StyleValue($style, $value);
    }

    /**
     * @return StyleValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function createGroupedStyleValueList(StyleList $styleList): GroupedStyleValueList
    {
        $groupedList = new GroupedStyleValueList();
        foreach ($styleList->getSets() as $set) {
            $groupedSet = new GroupedStyleValueListSet($set->getName());
            foreach ($set->getStyles() as $style) {
                // Find the style in our value list that matches the one in the original nested set
                foreach($this->getValues() as $styleValue) {
                    if ($styleValue->getStyle()->getVariable() === $style->getVariable()) {
                        $groupedSet->addValue($styleValue);
                    }
                }
            }
            $groupedList->addSet($groupedSet);
        }
        return $groupedList;
    }


}
