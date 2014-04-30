<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Core;
use \Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Less_Tree_Color;
use Less_Tree_Call;
use Less_Tree_Dimension;
use View;
use Request;
class ColorStyle extends Style {

    public function render($value = false) {
        $color = '';
        if ($value) {
            $color = $value->toStyleString();
        }
        $inputName = $this->getVariable();
        $r = Request::getInstance();
        if ($r->request->has($inputName)) {
            $color = h($r->request->get($inputName));
        }

        $view = View::getInstance();
        $view->requireAsset('core/colorpicker');

        print "<input type=\"text\" name=\"{$inputName}\" value=\"{$color}\" id=\"ccm-colorpicker-{$inputName}\" />";
        print "<script type=\"text/javascript\">";
        print "$(function() { $('#ccm-colorpicker-{$inputName}').spectrum({showAlpha: true, value: '{$color}', change: function() {ConcreteEvent.publish('StyleCustomizerSave');}});});";
        print "</script>";
    }

    public static function parse($value) {
        if ($value instanceof Less_Tree_Color) {
            $cv = new ColorValue($value->rgb[0], $value->rgb[1], $value->rgb[2]);
        } else if ($value instanceof Less_Tree_Call) {
            // might be rgb() or rgba()
            if ($value->name == 'rgba') {
                $cv = new ColorValue(
                    $value->args[0]->value[0]->value,
                    $value->args[1]->value[0]->value,
                    $value->args[2]->value[0]->value,
                    $value->args[3]->value[0]->value
                );
            } else if ($value->name == 'rgb') {
                $cv = new ColorValue(
                    $value->args[0]->value[0]->value,
                    $value->args[1]->value[0]->value,
                    $value->args[2]->value[0]->value
                );
            }
        }

        return $cv;
    }


    public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list) {
        foreach($list->getRules() as $rule) {
            if ($rule->name == '@' . $this->getVariable() . '-color') {
                $value = $rule->value->value[0]->value[0];
                $cv = static::parse($value);
                return $cv;

            }
        }
    }

}