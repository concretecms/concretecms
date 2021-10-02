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
        $variable = $collection->getVariable($this->getVariable());
        if (!$variable) {
            // Legacy backward compatibility hack. The old customizer required that the "type" of the variable
            // be the variable suffix. So the `page-background` variable is written as `page-background-color`
            // in the .less file. Let's check to see if this exists. Note to devs: you should NOT use this
            // convention going forward. Just name your variables the same in the .xml file and the .less/.sass
            // files. Note, this is only required on color, size, image, and type styles, because those are the
            // only types of variables available to the legacy customizer.
            $variable = $collection->getVariable($this->getVariable() . '-color');
        }
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
        $variable = new Variable($this->getVariable(), $variableValue);
        return $variable;
    }

}
