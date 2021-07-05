<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Parser\Normalizer\NormalizedVariableCollection;
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

}
