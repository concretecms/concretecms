<?php
namespace Concrete\Core\Form\Service\Widget;
use View;
class Typography
{
    /**
     * Creates form fields and JavaScript includes to add a font picker widget.
     * <code>
     *     $dh->output('background-color', '#f00');
     * </code>
     * @param string $inputName
     * @param array  $value
     * @param array  $options
     */
    public function output($inputName, $value = array(), $options = array())
    {
        $view = View::getInstance();
        $view->requireAsset('core/style-customizer');

        $options['inputName'] = $inputName;
        $options = array_merge($options, $value);
        $strOptions = json_encode($options);

        print '<span class="ccm-style-customizer-display-swatch-wrapper" data-font-selector="' . $inputName . '"></span>';
        print "<script type=\"text/javascript\">";
        print "$(function () { $('span[data-font-selector={$inputName}]').concreteTypographySelector({$strOptions}); })";
        print "</script>";

    }

}
