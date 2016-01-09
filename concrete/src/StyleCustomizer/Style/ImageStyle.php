<?php
namespace Concrete\Core\StyleCustomizer\Style;

use \Concrete\Core\StyleCustomizer\Style\Value\ImageValue;
use Less_Environment;
use File;
use Loader;
use Permissions;

class ImageStyle extends Style
{
    public function render($value = false)
    {
        $r = \Concrete\Core\Http\ResponseAssetGroup::get();
        $r->requireAsset('core/style-customizer');

        $strOptions = '';
        $i = 0;
        $options['inputName'] = $this->getVariable();
        if (is_object($value)) {
            $options['value'] = $value->getUrl();
        }
        $strOptions = json_encode($options);

        print '<span class="ccm-style-customizer-display-swatch-wrapper" data-image-selector="' . $this->getVariable() . '"></span>';
        print "<script type=\"text/javascript\">";
        print "$(function() { $('span[data-image-selector=" . $this->getVariable() . "]').concreteStyleCustomizerImageSelector({$strOptions}); });";
        print "</script>";
    }

    public function getValueFromRequest(\Symfony\Component\HttpFoundation\ParameterBag $request)
    {
        $image = $request->get($this->getVariable());
        $image = $image['image'];
        if ($image) {
            $iv = new ImageValue($this->getVariable());
            if (Loader::helper('validation/numbers')->integer($image)) {
                // it's a file ID.
                $f = File::getByID($image);
                if (is_object($f)) {
                    $fp = new Permissions($f);
                    if ($fp->canViewFile()) {
                        $iv->setFileID($image);
                        $iv->setUrl($f->getRelativePath());
                    }
               }
            } else {
                $iv->setUrl($image);
            }

            return $iv;
        }
    }

    public function getValuesFromVariables($rules = array())
    {
        $values = array();
        foreach ($rules as $rule) {
            if (preg_match('/@(.+)\-image/i', isset($rule->name) ? $rule->name : '', $matches)) {
                $entryURI = $rule->value->value[0]->value[0]->currentFileInfo['entryUri'];
                $value = $rule->value->value[0]->value[0]->value;
                if ($entryURI) {
                    $value = Less_Environment::normalizePath($entryURI . $value);
                }
                $iv = new ImageValue($matches[1]);
                $iv->setUrl($value);
                if (is_object($iv)) {
                    $values[] = $iv;
                }
            }
        }

        return $values;
    }

}
