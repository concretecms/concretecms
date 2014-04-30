<?php
namespace Concrete\Core\StyleCustomizer\Style;

use \Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use \Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use \Concrete\Core\StyleCustomizer\Style\ColorStyle;
use \Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use \Concrete\Core\StyleCustomizer\Style\SizeStyle;

use Core;

class TypeStyle extends Style {

    public function render()
    {
        $fh = Core::make('helper/form/font');
        print $fh->output($this->getVariable(), '', array());
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