<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use Concrete\Core\Support\Facade\Application;
use Primal\Color\Parser;
use Symfony\Component\HttpFoundation\ParameterBag;

class TypeStyle extends Style
{
    /**
     * @param \Concrete\Core\StyleCustomizer\Style\Value\TypeValue|null|false $style
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::render()
     */
    public function render($style = false)
    {
        $app = Application::getFacadeApplication();
        $fh = $app->make('helper/form/font');
        $args = [];
        if ($style) {
            $args['fontFamily'] = $style->getFontFamily();
            $color = $style->getColor();
            if ($color) {
                $args['color'] = $color->toStyleString();
            }
            $args['fontWeight'] = $style->getFontWeight();
            if ($style->getFontStyle() != -1) {
                $args['italic'] = $style->getFontStyle() == 'italic' ? true : false;
            }
            if ($style->getTextDecoration() != -1) {
                $args['underline'] = $style->getTextDecoration() == 'underline' ? true : false;
            }
            if ($style->getTextTransform() != -1) {
                $args['uppercase'] = $style->getTextTransform() == 'uppercase' ? true : false;
            }
            $fontSize = $style->getFontSize();
            if (is_object($fontSize)) {
                $args['fontSizeValue'] = $fontSize->getSize();
                $args['fontSizeUnit'] = $fontSize->getUnit();
            }
            $letterSpacing = $style->getLetterSpacing();
            if (is_object($letterSpacing)) {
                $args['letterSpacingValue'] = $letterSpacing->getSize();
                $args['letterSpacingUnit'] = $letterSpacing->getUnit();
            }
            $lineHeight = $style->getLineHeight();
            if (is_object($lineHeight)) {
                $args['lineHeightValue'] = $lineHeight->getSize();
                $args['lineHeightUnit'] = $lineHeight->getUnit();
            }
        }
        $fh->output($this->getVariable(), $args, []);
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\TypeValue
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValueFromRequest()
     */
    public function getValueFromRequest(ParameterBag $request)
    {
        $type = $request->get($this->getVariable(), []);
        $tv = new TypeValue($this->getVariable());
        if (!empty($type['font-family'])) {
            $tv->setFontFamily($type['font-family']);
        }
        if (!empty($type['font-weight'])) {
            $tv->setFontWeight($type['font-weight']);
        }
        if (!empty($type['italic'])) {
            $tv->setFontStyle('italic');
        } elseif (isset($type['italic'])) {
            $tv->setFontStyle('none');
        }

        if (!empty($type['underline'])) {
            $tv->setTextDecoration('underline');
        } elseif (isset($type['underline'])) {
            $tv->setTextDecoration('none');
        }

        if (!empty($type['uppercase'])) {
            $tv->setTextTransform('uppercase');
        } elseif (isset($type['uppercase'])) {
            $tv->setTextTransform('none');
        }

        if (!empty($type['color'])) {
            $cv = new Parser($type['color']);
            $result = $cv->getResult();
            $alpha = false;
            if ($result->alpha && $result->alpha < 1) {
                $alpha = $result->alpha;
            }
            $cvv = new ColorValue();
            $cvv
                ->setRed($result->red)
                ->setGreen($result->green)
                ->setBlue($result->blue)
                ->setAlpha($alpha)
            ;
            $tv->setColor($cvv);
        }

        if (!empty($type['font-size']) && isset($type['font-size']['size'])) {
            $sv = new SizeValue();
            $sv->setSize($type['font-size']['size']);
            if (!empty($type['font-size']['unit'])) {
                $sv->setUnit($type['font-size']['unit']);
            }
            $tv->setFontSize($sv);
        }

        if (!empty($type['letter-spacing']) && isset($type['letter-spacing']['size'])) {
            $sv = new SizeValue();
            $sv->setSize($type['letter-spacing']['size']);
            if (!empty($type['letter-spacing']['unit'])) {
                $sv->setUnit($type['letter-spacing']['unit']);
            }
            $tv->setLetterSpacing($sv);
        }

        if (!empty($type['line-height']) && isset($type['line-height']['size'])) {
            $sv = new SizeValue();
            $sv->setSize($type['line-height']['size']);
            if (!empty($type['line-height']['unit'])) {
                $sv->setUnit($type['line-height']['unit']);
            }
            $tv->setLineHeight($sv);
        }

        return $tv;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\TypeValue[]
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValuesFromVariables()
     */
    public static function getValuesFromVariables($rules = [])
    {
        $values = [];

        foreach ($rules as $rule) {
            $ruleName = isset($rule->name) ? $rule->name : '';
            if (preg_match('/@(.+)\-type-font-family/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setFontFamily($value);
            }
            if (preg_match('/@(.+)\-type-font-weight/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setFontWeight($value);
            }
            if (preg_match('/@(.+)\-type-text-decoration/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setTextDecoration($value);
            }

            if (preg_match('/@(.+)\-type-text-transform/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setTextTransform($value);
            }
            if (preg_match('/@(.+)\-type-font-style/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setFontStyle($value);
            }
            if (preg_match('/@(.+)\-type-color/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $cv = ColorStyle::parse($value);
                if ($cv instanceof ColorValue) {
                    $values[$matches[1]]->setColor($cv);
                }
            }

            if (preg_match('/@(.+)\-type-font-size/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $values[$matches[1]]->setFontSize($sv);
                }
            }

            if (preg_match('/@(.+)\-type-letter-spacing/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $values[$matches[1]]->setLetterSpacing($sv);
                }
            }

            if (preg_match('/@(.+)\-type-line-height/i', $ruleName, $matches)) {
                if (!isset($values[$matches[1]])) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $values[$matches[1]]->setLineHeight($sv);
                }
            }
        }

        return $values;
    }
}
