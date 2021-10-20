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
        $variable = $collection->getVariable($this->getVariableToInspect());
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
        $variable = new NumberVariable($this->getVariableToInspect(), $value->getSize(), $value->getUnit());
        return $variable;
    }

}
