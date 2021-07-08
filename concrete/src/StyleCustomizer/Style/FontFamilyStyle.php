<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\FontFamilyValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

class FontFamilyStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariable());
        if ($variable) {
            $value = new FontFamilyValue($variable->getValue());
            return $value;
        }
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        foreach ($styles as $style) {
            if (isset($style['variable']) && $style['variable'] == $this->getVariable()) {
                $value = new FontFamilyValue($style['value']['fontFamily']);
                return $value;
            }
        }
        return null;
    }

    /**
     * @param FontFamilyValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $variableValue = sprintf('font-family: %s', $value->getFontFamily());
        $variable = new Variable($this->getVariable(), $variableValue);
        return $variable;
    }

}
