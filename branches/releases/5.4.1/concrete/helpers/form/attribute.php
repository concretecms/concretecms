<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class FormAttributeHelper {
	
	private $obj;
	
	public function setAttributeObject($obj) {
		$this->obj = $obj;
	}
	
	public function display($key, $required = false, $includeLabel = true) {
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
			$obj = call_user_func_array(array($class, 'getByHandle'), array($key));
		}
		
		if (!is_object($obj)) {
			return false;
		}
		
		if (is_object($this->obj)) {
			$value = $this->obj->getAttributeValueObject($obj);
		}
		$html = '';
		if ($includeLabel || $required) {
			$html .= '<div>';
		}
		if ($includeLabel) {
			$html .= $obj->render('label', false, true);
		}
		if ($required) {
			$html .= ' <span class="ccm-required">*</span>';
		}
		if ($includeLabel || $required) {
			$html .= '</div>';
		}
		$html .= $obj->render('form', $value, true);
		return $html;
	}

}