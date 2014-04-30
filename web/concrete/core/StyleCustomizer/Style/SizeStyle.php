<?php
namespace Concrete\Core\StyleCustomizer\Style;
use \Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Less_Tree_Dimension;

class SizeStyle extends Style {

    public function render() {
        $r = \Concrete\Core\Http\ResponseAssetGroup::get();
        $r->requireAsset('core/style-customizer');

        $strOptions = '';
        $i = 0;
        $options['inputName'] = $this->getVariable();
        foreach($options as $key => $value) {
            if ($i == 0) $strOptions = '{';
            $strOptions .= $key . ':\'' . $value . '\'';
            if (($i + 1) == count($strOptions)) $strOptions .= '}';
        }

        print '<div data-size-selector="' . $this->getVariable() . '"></div>';
        print "<script type=\"text/javascript\">";
        print "$(function() { $('div[data-size-selector=" . $this->getVariable() . "]').concreteSizeSelector({$strOptions}); });";
        print "</script>";
    }

    public static function parse($value) {
        if ($value instanceof Less_Tree_Dimension) {
            $unit = 'px';
            if (isset($value->unit->numerator[0])) {
                $unit = $value->unit->numerator[0];
            }

            $sv = new SizeValue($value->value, $unit);
        }
        return $sv;
    }

    public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list) {
        foreach($list->getRules() as $rule) {
            if ($rule->name == '@' . $this->getVariable() . '-size') {
                $value = $rule->value->value[0]->value[0];
                $sv = static::parse($value);
                return $sv;
            }
        }
    }


}

