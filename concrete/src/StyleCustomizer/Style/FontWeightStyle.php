<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\FontFamilyValue;
use Concrete\Core\StyleCustomizer\Style\Value\FontWeightValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

class FontWeightStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariable());
        if ($variable) {
            $value = new FontWeightValue($variable->getValue());
            return $value;
        }
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        foreach ($styles as $style) {
            if (isset($style['variable']) && $style['variable'] == $this->getVariable()) {
                $value = new FontWeightValue($style['value']['fontWeight']);
                return $value;
            }
        }
        return null;
    }

    /**
     * @param FontWeightValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $variable = new Variable($this->getVariable(), $value->getFontWeight());
        return $variable;
    }

}
