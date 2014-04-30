<?php
namespace Concrete\Core\StyleCustomizer\Style;
use \Concrete\Core\StyleCustomizer\Style\Value\ImageValue;

class ImageStyle extends Style {

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

        print '<div data-image-selector="' . $this->getVariable() . '"></div>';
        print "<script type=\"text/javascript\">";
        print "$(function() { $('div[data-image-selector=" . $this->getVariable() . "]').concreteStyleCustomizerImageSelector({$strOptions}); });";
        print "</script>";
    }

    public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list) {
        foreach($list->getRules() as $rule) {
            if ($rule->name == '@' . $this->getVariable() . '-image') {
                $value = $rule->value->value[0]->value[0]->value;
                $iv = new ImageValue();
                $iv->setPath($value);
                return $iv;
            }
        }
    }


}

