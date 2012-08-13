<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Form_Attribute {
	
	protected $obj;
	
	public function reset() {
		unset($this->obj);
	}
	
	public function setAttributeObject($obj) {
		$this->obj = $obj;
	}
	
	public function display($key, $required = false, $includeLabel = true, $template = 'composer') {
		if (is_object($key)) {
			$obj = $key;
		} else {
			$oclass = get_class($this->obj);
			switch($oclass) {
				case 'UserInfo':
					$class = 'UserAttributeKey';
					break;
				default:
					$class = $oclass . 'AttributeKey';
					break;
			}
			$obj = call_user_func(array($class, 'getByHandle'), $key);
		}
		
		if (!is_object($obj)) {
			return false;
		}
		
		if (is_object($this->obj)) {
			$value = $this->obj->getAttributeValueObject($obj);
		}
		$html = '<div class="control-group">';
		if ($includeLabel) {
			$html .= $obj->render('label', false, true);
		}
		if ($required) {
			$html .= ' <span class="ccm-required">*</span>';
		}
		$html .= '<div class="controls">';
		$html .= $obj->render($template, $value, true);
			
		$html .= '</div></div>';
		
		return $html;
	}

}