<?
namespace Concrete\Core\Page\Theme\EditableStyle;
use Loader;
/** 
 * A class specifically for editable fonts
 */
class FontEditableStyle extends EditableStyle {
	
	public function getFamily() {return $this->family;}
	public function getSize() {return $this->size;}
	public function getWeight() {return $this->weight;}
	public function getStyle() {return $this->style;}
	
	public function __construct($value) {
		parent::__construct($value);
		
		// value is pretty rigid. Has to be "font: normal normal 18px Book Antiqua"
		// so font: $weight $
		
		$expl = explode(' ', $this->pThemeStyleValue);
		$this->style = trim($expl[0]);
		$this->weight = trim($expl[1]);
		$this->size = preg_replace('/[^0-9]/', '', trim($expl[2]));
		$this->family = trim($expl[3]);
		if (count($expl) > 4) {
			for ($i = 4; $i < count($expl); $i++) {
				$this->family .= ' ' . trim($expl[$i]);
			}
		}
		
	}
	
	public function getShortValue() {
		return $this->style . '|' . $this->weight . '|' . $this->size . '|' . $this->family;
	}
}