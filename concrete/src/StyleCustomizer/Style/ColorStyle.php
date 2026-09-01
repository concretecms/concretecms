<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\ColorVariable;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Primal\Color\Parser;
use Primal\Color\RGBColor;

class ColorStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariableToInspect());
        if ($variable) {
            try {
                $color = new Parser($variable->getValue());
            } catch (\Exception $e) {
                $color = new Parser(
                    (new ColorVariable(255, 255, 255, 255, 0))
                    ->getValue()
                );
            }
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
                $value->setRed($style['value']['r'] ?? null);
                $value->setGreen($style['value']['g'] ?? null);
                $value->setBlue($style['value']['b'] ?? null);
                $value->setAlpha($style['value']['a'] ?? null);
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
            $this->clampChannel($value->getRed()),
            $this->clampChannel($value->getGreen()),
            $this->clampChannel($value->getBlue()),
            $this->clampAlpha($value->getAlpha())
        );
        $variable = new Variable($this->getVariableToInspect(), $variableValue);
        return $variable;
    }

    /**
     * Clamp a color channel value to an integer between 0 and 255.
     *
     * @param mixed $channel
     *
     * @return int
     */
    private function clampChannel($channel): int
    {
        if (!is_numeric($channel)) {
            $channel = 0;
        }
        return (int) max(0, min(255, $channel));
    }

    /**
     * Clamp an alpha value to a float between 0 and 1.
     *
     * @param mixed $alpha
     *
     * @return float
     */
    private function clampAlpha($alpha): float
    {
        if (!is_numeric($alpha)) {
            $alpha = 1;
        }
        return max(0, min(1, (float) $alpha));
    }

}
