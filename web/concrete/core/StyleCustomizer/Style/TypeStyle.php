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
            $args['bold'] = $style->getFontWeight() == 'bold' ? true : false;
            $args['italic'] = $style->getFontStyle() == 'italic' ? true : false;
            $args['underline'] = $style->getTextDecoration() == 'underline' ? true : false;
            $args['uppercase'] = $style->getTextTransform() == 'uppercase' ? true : false;
            $fontSize = $style->getFontSize();
            if (is_object($fontSize)) {
                $args['fontSize'] = array('value' => $fontSize->getSize(), 'unit' => $fontSize->getUnit());
            }
            $letterSpacing = $style->getLetterSpacing();
            if (is_object($letterSpacing)) {
                $args['letterSpacing'] = array('value' => $letterSpacing->getSize(), 'unit' => $letterSpacing->getUnit());
            }
            $lineHeight = $style->getLineHeight();
            if (is_object($lineHeight)) {
                $args['lineHeight'] = array('value' => $lineHeight->getSize(), 'unit' => $lineHeight->getUnit());
            }
        }
        print $fh->output($this->getVariable(), $args, array());
    }

    protected function ruleMatches($rule, $variable) {
        return $rule->name == '@' . $this->getVariable() . '-type-' . $variable;
    }

    public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list)
    {
        $fv = false;
        foreach($list->getRules() as $rule) {
            if ($this->ruleMatches($rule, 'font-family')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0]->value;
                $fv->setFontFamily($value);
            }
            if ($this->ruleMatches($rule, 'font-weight')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0]->value;
                $fv->setFontWeight($value);
            }
            if ($this->ruleMatches($rule, 'text-decoration')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0]->value;
                $fv->setTextDecoration($value);
            }

            if ($this->ruleMatches($rule, 'text-transform')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0]->value;
                $fv->setTextTransform($value);
            }
            if ($this->ruleMatches($rule, 'font-style')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0]->value;
                $fv->setFontStyle($value);
            }
            if ($this->ruleMatches($rule, 'color')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0];
                $cv = ColorStyle::parse($value);
                if ($cv instanceof ColorValue) {
                    $fv->setColor($cv);
                }
            }

            if ($this->ruleMatches($rule, 'font-size')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $fv->setFontSize($sv);
                }
            }

            if ($this->ruleMatches($rule, 'letter-spacing')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $fv->setLetterSpacing($sv);
                }
            }

            if ($this->ruleMatches($rule, 'line-height')) {
                if (!$fv) {
                    $fv = new TypeValue();
                }
                $value = $rule->value->value[0]->value[0];
                $sv = SizeStyle::parse($value);
                if ($sv instanceof SizeValue) {
                    $fv->setLineHeight($sv);
                }
            }


        }
        return $fv;
    }


}