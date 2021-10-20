<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\PresetFontsFileValue;
use Concrete\Core\StyleCustomizer\Style\Value\Value;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

/**
 * @deprecated
 */
class PresetFontsFileStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        return null;
    }

    /**
     * @param PresetFontsFileValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $variable = new Variable('preset-fonts-file', $value->getValue());
        return $variable;
    }

}
