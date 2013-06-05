<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 *
 * A page theme editable style object corresponds to a style in a stylesheet that is able to be manipulated through the dashboard.
 * @package Pages
 * @subpackage Themes
 */
class Concrete5_Model_PageThemeEditableStyle extends Object {
	
	const TSTYPE_COLOR = 1;
	const TSTYPE_FONT = 10;
	const TSTYPE_CUSTOM = 20;
	
	public function getHandle() {return $this->ptsHandle;}
	public function getOriginalValue() {return $this->ptsOriginalValue;}
	public function getValue() {return $this->ptsValue;}
	public function getProperty() {
		// the original property that the stylesheet defines, like background-color, etc...
		return $this->ptsProperty;
	}
	
	public function getType() {return $this->ptsType;}
	public function getName() {
		$h = Loader::helper('text');
		return $h->unhandle($this->ptsHandle);
	}
	
	public function __construct($value = '') {
		if ($value) {
			$this->ptsValue = trim($value);
			$this->ptsOriginalValue = trim($value);
			$this->ptsProperty = substr($this->ptsValue, 0, strpos($this->ptsValue, ':'));
			$this->ptsValue = substr($this->ptsValue, strpos($this->ptsValue, ':') + 1);
			$this->ptsValue = trim(str_replace(';', '', $this->ptsValue));
		}
	}
}
