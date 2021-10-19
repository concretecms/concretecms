<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Primal\Color\Parser;

class ColorStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariableToInspect());
        if ($variable) {
            $color = new Parser($variable->getValue());
            if ($color) {
                $result = $color->getResult();
                $alpha = 1;
                if (is_numeric($result->alpha) && $result->alpha >= 0 && $result->alpha < 1) {
                    $alpha = $result->alpha;
                }
                $colorValue = new ColorValue();
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

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        foreach ($styles as $style) {
            if (isset($style['variable']) && $style['variable'] == $this->getVariable()) {
                $value = new ColorValue();
                $value->setRed($style['value']['r']);
                $value->setGreen($style['value']['g']);
                $value->setBlue($style['value']['b']);
                $value->setAlpha($style['value']['a']);
                return $value;
            }
        }
        return null;
    }

    /**
     * @param ColorValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $variableValue = sprintf(
            'rgba(%s, %s, %s, %s)',
            $value->getRed(),
            $value->getGreen(),
            $value->getBlue(),
            $value->getAlpha()
        );
        $variable = new Variable($this->getVariableToInspect(), $variableValue);
        return $variable;
    }

}
