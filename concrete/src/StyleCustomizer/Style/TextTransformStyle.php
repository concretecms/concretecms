<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\FontFamilyValue;
use Concrete\Core\StyleCustomizer\Style\Value\TextTransformValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

class TextTransformStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariable());
        if ($variable) {
            $value = new TextTransformValue($variable->getValue());
            return $value;
        }
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        foreach ($styles as $style) {
            if (isset($style['variable']) && $style['variable'] == $this->getVariable()) {
                $value = new TextTransformValue($style['value']['textTransform']);
                return $value;
            }
        }
        return null;
    }

    /**
     * @param TextTransformValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $variable = new Variable($this->getVariable(), $value->getTextTransform());
        return $variable;
    }

}
