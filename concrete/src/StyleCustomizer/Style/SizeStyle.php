<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\NumberVariable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

class SizeStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariable());
        if (!$variable) {
            // Legacy backward compatibility hack. The old customizer required that the "type" of the variable
            // be the variable suffix. So the `page-background` variable is written as `page-background-color`
            // in the .less file. Let's check to see if this exists. Note to devs: you should NOT use this
            // convention going forward. Just name your variables the same in the .xml file and the .less/.sass
            // files. Note, this is only required on color, size, image, and type styles, because those are the
            // only types of variables available to the legacy customizer.
            $variable = $collection->getVariable($this->getVariable() . '-size');
        }
        if ($variable) {
            /**
             * @var $variable NumberVariable
             */
            $value = new SizeValue($variable->getNumber(), $variable->getUnit());
            return $value;
        }
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        foreach ($styles as $style) {
            if (isset($style['variable']) && $style['variable'] == $this->getVariable()) {
                $value = new SizeValue($style['value']['size'], $style['value']['unit']);
                return $value;
            }
        }
        return null;
    }

    /**
     * @param SizeValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $variable = new NumberVariable($this->getVariable(), $value->getSize(), $value->getUnit());
        return $variable;
    }

}
