<?php
namespace Concrete\Core\StyleCustomizer\Style;

use \Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use \Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use \Concrete\Core\StyleCustomizer\Style\ColorStyle;
use \Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use \Concrete\Core\StyleCustomizer\Style\SizeStyle;

use Core;

class TypeStyle extends Style {

    public function render($style = false)
    {
        $fh = Core::make('helper/form/font');
        $args = array();
        if (is_object($style)) {
            $args['fontFamily'] = $style->getFontFamily();
            $color = $style->getColor();
            if (is_object($color)) {
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
        print $fh->output($this->getVariable(), $args, array());
    }

    public function getValueFromRequest(\Symfony\Component\HttpFoundation\ParameterBag $request)
    {
        $type = $request->get($this->getVariable());
        $tv = new TypeValue($this->getVariable());
        if ($type['font-family']) {
            $tv->setFontFamily($type['font-family']);
        }
        if ($type['font-weight']) {
            $tv->setFontWeight($type['font-weight']);
        }
        if ($type['italic']) {
            $tv->setFontStyle('italic');
        } else if (isset($type['italic'])) {
            $tv->setFontStyle('none');
        }

        if ($type['underline']) {
            $tv->setTextDecoration('underline');
        } else if (isset($type['underline'])) {
            $tv->setDecoration('none');
        }

        if ($type['uppercase']) {
            $tv->setTextTransform('uppercase');
        } else if (isset($type['uppercase'])) {
            $tv->setTextTransform('none');
        }

        if ($type['color']) {
            $cv = new \Primal\Color\Parser($type['color']);
            $result = $cv->getResult();
            $alpha = false;
            if ($result->alpha && $result->alpha < 1) {
                $alpha = $result->alpha;
            }
            $cvv = new ColorValue();
            $cvv->setRed($result->red);
            $cvv->setGreen($result->green);
            $cvv->setBlue($result->blue);
            $cvv->setAlpha($alpha);
            $tv->setColor($cvv);
        }

        if ($type['font-size']) {
            $sv = new SizeValue();
            $sv->setSize($type['font-size']['size']);
            if ($type['font-size']['unit']) {
                $sv->setUnit($type['font-size']['unit']);
            }
            $tv->setFontSize($sv);
        }

        if ($type['letter-spacing']) {
            $sv = new SizeValue();
            $sv->setSize($type['letter-spacing']['size']);
            if ($type['letter-spacing']['unit']) {
                $sv->setUnit($type['letter-spacing']['unit']);
            }
            $tv->setLetterSpacing($sv);
        }

        if ($type['line-height']) {
            $sv = new SizeValue();
            $sv->setSize($type['line-height']['size']);
            if ($type['line-height']['unit']) {
                $sv->setUnit($type['line-height']['unit']);
            }
            $tv->setLineHeight($sv);
        }

        return $tv;
    }

    public function getValuesFromVariables($rules = array())
    {
        $values = array();

        foreach($rules as $rule) {
            if (preg_match('/@(.+)\-type-font-family/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setFontFamily($value);
            }
            if (preg_match('/@(.+)\-type-font-weight/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setFontWeight($value);
            }
            if (preg_match('/@(.+)\-type-text-decoration/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setTextDecoration($value);
            }

            if (preg_match('/@(.+)\-type-text-transform/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setTextTransform($value);
            }
            if (preg_match('/@(.+)\-type-font-style/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0]->value;
                $values[$matches[1]]->setFontStyle($value);
            }
            if (preg_match('/@(.+)\-type-color/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $cv = ColorStyle::parse($value);
                if ($cv instanceof ColorValue) {
                    $values[$matches[1]]->setColor($cv);
                }
            }

            if (preg_match('/@(.+)\-type-font-size/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $values[$matches[1]]->setFontSize($sv);
                }
            }

            if (preg_match('/@(.+)\-type-letter-spacing/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
                    $values[$matches[1]] = new TypeValue($matches[1]);
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $values[$matches[1]]->setLetterSpacing($sv);
                }
            }

            if (preg_match('/@(.+)\-type-line-height/i', $rule->name, $matches)) {
                if (!$values[$matches[1]]) {
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