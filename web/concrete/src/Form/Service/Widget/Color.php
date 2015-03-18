<?php
namespace Concrete\Core\Form\Service\Widget;
use Loader;
use View;
use Request;
class Color
{
    /**
     * Creates form fields and JavaScript includes to add a color picker widget.
     * <code>
     *     $dh->output('background-color', '#f00');
     * </code>
     * @param string $inputName
     * @param value  $string
     * @param array  $options
     */
    public function output($inputName, $value = null, $options = array())
    {
        $view = View::getInstance();
        $view->requireAsset('core/colorpicker');
        $form = Loader::helper('form');
        $r = Request::getInstance();
        if ($r->request->has($inputName)) {
            $value = h($r->request->get($inputName));
        }
        $strOptions = '';
        $i = 0;
        $defaults = array();
        $defaults['value'] = $value;
        $defaults['className'] = 'ccm-widget-colorpicker';
        $defaults['showInitial'] = true;
        $defaults['showInput'] = true;
        $defaults['allowEmpty'] = true;
        $defaults['cancelText'] = t('Cancel');
        $defaults['chooseText'] = t('Choose');
        $defaults['preferredFormat'] = 'rgb';
        $defaults['showAlpha'] = false;
        $defaults['clearText'] = t('Clear Color Selection');
        $defaults['appendTo'] = '.ui-dialog';
        $strOptions = json_encode(array_merge($defaults, $options));

        print "<input type=\"text\" name=\"{$inputName}\" value=\"{$value}\" id=\"ccm-colorpicker-{$inputName}\" />";
        print "<script type=\"text/javascript\">";
        print "$(function () { $('#ccm-colorpicker-{$inputName}').spectrum({$strOptions}); })";
        print "</script>";
    }

}
