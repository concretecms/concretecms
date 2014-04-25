<?
namespace Concrete\Core\Form\Service\Widget;
use Loader;
use View;
use Request;
class Color {

	
	/** 
	 * Creates form fields and JavaScript includes to add a color picker widget.
	 * <code>
	 *     $dh->output('background-color', '#f00');
	 * </code>
	 * @param string $fieldFormName
	 * @param string $fieldLabel
	 * @param string $value
	 * @param bool $includeJavaScript
	 */
	public function output($inputName, $value = null) {
		$html = '';
		$view = View::getInstance();
		$view->requireAsset('core/colorpicker');
		$form = Loader::helper('form');
		$r = Request::getInstance();
		if ($r->request->has($inputName)) {
			$value = h($r->request->get($inputName));
		}
		print "<input type=\"text\" name=\"{$inputName}\" value=\"{$value}\" id=\"ccm-colorpicker-{$inputName}\" />";
		print "<script type=\"text/javascript\">";
		print "$(function() { $('#ccm-colorpicker-{$inputName}').spectrum(); })";
		print "</script>";
	}
	
	
}