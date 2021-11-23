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
        $this->add(new StyleValue($style, $value));
    }

    public function add(StyleValue $styleValue)
    {
        $this->values[] = $styleValue;
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
                    if (($styleValue->getStyle()->getVariable() === $style->getVariable()) &&
                        (get_class($styleValue->getStyle()) === get_class($style))) {
                            // This get_class() check shouldn't be necessary, but some legacy styles
                            // actually have multiple instances of the SAME variable within the style, because
                            // if the type is different you can technically do that. In the legacy customizer
                            // different types will let you use different computed variables. e.g.
                            // if my style variable is "body-text" I could have a variable of the type color and use
                            // "body-text-color" and a variable of the type "type" and use "body-text-type" both
                            // in the same styles.xml.
                            $groupedSet->addValue($styleValue);
                    }
                }
            }
            $groupedList->addSet($groupedSet);
        }
        return $groupedList;
    }


}
