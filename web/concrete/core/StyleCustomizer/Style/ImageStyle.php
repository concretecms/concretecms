<?php
namespace Concrete\Core\StyleCustomizer\Style;
use \Concrete\Core\StyleCustomizer\Style\Value\ImageValue;
use Less_Environment;
class ImageStyle extends Style {

    public function render($value = false) {
        $r = \Concrete\Core\Http\ResponseAssetGroup::get();
        $r->requireAsset('core/style-customizer');

        $strOptions = '';
        $i = 0;
        $options['inputName'] = $this->getVariable();
        if (is_object($value)) {
            $options['value'] = $value->getUrl();
        }
        $strOptions = json_encode($options);

        print '<div data-image-selector="' . $this->getVariable() . '"></div>';
        print "<script type=\"text/javascript\">";
        print "$(function() { $('div[data-image-selector=" . $this->getVariable() . "]').concreteStyleCustomizerImageSelector({$strOptions}); });";
        print "</script>";
    }

    public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list) {
        foreach($list->getRules() as $rule) {
            if ($rule->name == '@' . $this->getVariable() . '-image') {
                $entryURI = $rule->value->value[0]->value[0]->currentFileInfo['entryUri'];
                $value = $rule->value->value[0]->value[0]->value;
                if ($entryURI) {
                    $value = Less_Environment::normalizePath($entryURI . $value);
                }
                $iv = new ImageValue();
                $iv->setUrl($value);
                return $iv;
            }
        }
    }


}

