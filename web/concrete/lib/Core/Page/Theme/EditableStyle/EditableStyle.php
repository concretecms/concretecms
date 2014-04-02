<?
namespace Concrete\Core\Page\Theme\EditableStyle;
use \Concrete\Core\Foundation\Object;
/**
 *
 * A page theme editable style object corresponds to a style in a stylesheet that is able to be manipulated through the dashboard.
 * @package Pages
 * @subpackage Themes
 */
class EditableStyle extends Object {
	
	const TSTYPE_COLOR = 1;
	const TSTYPE_FONT = 10;
	const TSTYPE_CUSTOM = 20;
	
	public function getTypeHeaderName() {
		switch($this->pThemeStyleType) {
			case PageThemeEditableStyle::TSTYPE_COLOR: 
				return t('Colors');
				break;
			case PageThemeEditableStyle::TSTYPE_FONT:
				return t('Typography');
				break;
			case PageThemeEditableStyle::TSTYPE_CUSTOM:
				return t('Custom CSS');
				break;
		}
	}

	public function getFormFieldInputName() {
		return preg_replace('/-/i', '_', $this->getHandle() . '_' . $this->getType());
	}

	public function getHandle() {return $this->pThemeStyleHandle;}
	public function getOriginalValue() {return $this->pThemeStyleOriginalValue;}
	public function getValue() {return $this->pThemeStyleValue;}
	public function getProperty() {
		// the original property that the stylesheet defines, like background-color, etc...
		return $this->pThemeStyleProperty;
	}
	
	public function getType() {return $this->pThemeStyleType;}
	public function getName() {
		$h = Loader::helper('text');
		return $h->unhandle($this->pThemeStyleHandle);
	}
	
	public function __construct($value = '') {
		if ($value) {
			$this->pThemeStyleValue = trim($value);
			$this->pThemeStyleOriginalValue = trim($value);
			$this->pThemeStyleProperty = substr($this->pThemeStyleValue, 0, strpos($this->pThemeStyleValue, ':'));
			$this->pThemeStyleValue = substr($this->pThemeStyleValue, strpos($this->pThemeStyleValue, ':') + 1);
			$this->pThemeStyleValue = trim(str_replace(';', '', $this->pThemeStyleValue));
		}
	}
}
