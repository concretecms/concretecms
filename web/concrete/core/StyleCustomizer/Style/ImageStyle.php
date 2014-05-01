<?php
namespace Concrete\Core\StyleCustomizer\Style;
use \Concrete\Core\StyleCustomizer\Style\Value\ImageValue;
use Less_Environment;
use File;
use Permissions;

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

    public function getValueFromRequest(\Symfony\Component\HttpFoundation\ParameterBag $request)
    {
        $image = $request->get($this->getVariable());
        $fID = $image['fID'];
        if ($fID) {
            $f = File::getByID($fID);
            if (is_object($f)) {
                $fp = new Permissions($f);
                if ($fp->canViewFile()) {
                    $iv = new ImageValue();
                    $iv->setFileID($fID);
                    $iv->setUrl($f->getRelativePath());
                    return $iv;
                }
            }
        }
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

