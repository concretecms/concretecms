<?
namespace Concrete\Core\Form\Service\Widget;
use Loader;
use View;
use Request;
class Typography {


    /**
     * Creates form fields and JavaScript includes to add a font picker widget.
     * <code>
     *     $dh->output('background-color', '#f00');
     * </code>
     * @param string $fieldFormName
     * @param string $fieldLabel
     * @param string $value
     * @param bool $includeJavaScript
     */
    public function output($inputName, $args = array(), $options = array()) {
        $html = '';
        $view = View::getInstance();
        $view->requireAsset('core/style-customizer');

        $strOptions = '';
        $i = 0;
        $options['inputName'] = $inputName;
        $options['value'] = $args;
        $strOptions = json_encode($options);

        print '<span data-font-selector="' . $inputName . '"></span>';
        print "<script type=\"text/javascript\">";
        print "$(function() { $('span[data-font-selector={$inputName}]').concreteTypographySelector({$strOptions}); })";
        print "</script>";

    }


}