<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Parser\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\Style\Value\Value;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Primal\Color\Parser;
class ColorStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariable());
        if ($variable) {
            $color = new Parser($variable->getValue());
            if ($color) {
                $result = $color->getResult();
                $alpha = 1;
                if (is_numeric($result->alpha) && $result->alpha >= 0 && $result->alpha < 1) {
                    $alpha = $result->alpha;
                }
                $colorValue = new ColorValue($this->getVariable());
                $colorValue
                    ->setRed($result->red)
                    ->setGreen($result->green)
                    ->setBlue($result->blue)
                    ->setAlpha($alpha)
                ;
                return $colorValue;
            }
        }
        return null;
    }

}
