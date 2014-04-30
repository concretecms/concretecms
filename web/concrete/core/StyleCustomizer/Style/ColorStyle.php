<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Core;
use \Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Less_Tree_Color;
use Less_Tree_Call;
use Less_Tree_Dimension;
class ColorStyle extends Style {

    public function render($value = false) {
        $fh = Core::make('helper/form/color');
        $color = '';
        if ($value) {
            $color = $value->toStyleString();
        }
        print $fh->output($this->getVariable(), $color, array(
            'showAlpha' => true
        ));
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