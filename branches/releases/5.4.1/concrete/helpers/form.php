<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helpful functions for working with forms. Includes HTML input tags and the like
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class FormHelper {


	private $radioIndex = 1;
	private $selectIndex = 1;

	/** 
	 * Returns an action suitable for including in a form action property.
	 * @param string $action
	 * @param string $task
	 */ 
	public function action($action, $task = null) {
		return View::url($action, $task);
	}

	/** 
	 * Creates a submit button
	 * @param string $name
	 * @param string value
	 * @param array $fields Additional fields appended at the end of the submit button
	 * return string $html
	 */	 
	public function submit($name, $value, $fields = array(), $additionalClasses='') {
		$_fields = '';
		if (isset($fields['class'])) {
			$additionalClasses = $fields['class'];
			unset($fields['class']);
		}
		foreach($fields as $key => $fieldvalue) {
			$_fields .= $key . '="' . $fieldvalue . '" ';
		}
		$str = '<input type="submit" class="ccm-input-submit '.$additionalClasses.'" id="' . $name . '" name="' . $name . '" value="' . $value . '" ' . $_fields . ' />';
		return $str;
	}
	
	/** 
	 * Creates a label tag
	 * @param string $field
	 * @param string $name
	 * @return string $html
	 */
	public function label($field, $name) {
		$str = '<label for="' . $field . '">' . $name . '</label>';
		return $str;
	}
	
	/** 
	 * Creates a file input element
	 * @param string $key
	 */
	public function file($key) {
		$str = '<input type="file" name="' . $key . '" value="" />';
		return $str;
	}
	
	/**
	 * Creates a hidden form field. 
	 * @param string $key
	 * @param string $value
	 */
	public function hidden($key, $value = null) {
		$val = $this->getRequestValue($key);
		if ($val !== false && (!is_array($val))) {
			$value = $val;
		}
		$str = '<input type="hidden" name="' . $key . '" id="' . $key . '" value="' . $value . '" />';
		return $str;
	}
	
	/** 
	 * Creates an HTML checkbox
	 * @param string $field
	 * @param string $value
	 * @param bool $checked
	 * @return string $html
	 */
	public function checkbox($field, $value, $isChecked = false, $miscFields = array()) {

		$mf = '';
		if (is_array($miscFields)) {
			foreach($miscFields as $k => $v) {
				$mf .= $k . '="' . $v . '" ';
			}
		}
		$id = $field;
		$_field = $field;
		$_array = false;
		if ((strpos($field, '[]') + 2) == strlen($field)) {
			$_field = substr($field, 0, strpos($field, '[]'));
			$id = $_field . '_' . $value;
			$_array = true;
		}

		if ($isChecked && (!isset($_REQUEST[$_field])) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$checked = true;
		} else if ($this->getRequestValue($field) == $value) {
			$checked = true;
		} else if (is_array($this->getRequestValue($field)) && in_array($value, $this->getRequestValue($field))) {
			$checked = true;
		}
			
		if ($checked) {
			$checked = 'checked="checked" ';
		}
		
		$str = '<input type="checkbox" class="ccm-input-checkbox" name="' . $field . '" id="' . $id . '" value="' . $value . '" ' . $checked . ' ' . $mf . ' />';
		return $str;
	}

	/** 
	 * Creates a textarea field. Second argument is either the value of the field (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return string $html
	 */
	 public function textarea($key) {
	 	$a = func_get_args();
		
		$str = '<textarea id="' . $key . '" name="' . $key . '" ';
		$rv = $this->getRequestValue($key);
		
		if (count($a) == 3) {
			$innerValue = ($rv !== false) ? $rv : $a[1];

			$miscFields = $a[2];
		} else {
			
			if (is_array($a[1])) {
				$innerValue = ($rv !== false) ? $rv : '';
				$miscFields = $a[1];
			} else {
				
				// we ignore this second value if a post is set with this guy in it
				$innerValue = ($rv !== false) ? $rv : $a[1];
			}
		}
		
		if (is_array($miscFields)) {
			if (empty($miscFields['class'])) {
				$miscFields['class'] = "ccm-input-textarea";
			} else {
				$miscFields['class'] .= " ccm-input-textarea";
			}

			foreach($miscFields as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		} else {
			$str .= ' class="ccm-input-textarea" ';
		}
		
		$str .= '>' . $innerValue . '</textarea>';
		return $str;
	 }
	 
	/**
	 * Generates a radio button
	 * @param string $key
	 * @param string $valueOfButton
	 * @param string $valueOfSelectedOption
	 */
	public function radio($key, $value, $valueOrArray = false, $miscFields = array()) {
		$str = '<input type="radio" class="ccm-input-radio" name="' . $key . '" id="' . $key . $this->radioIndex . '" value="' . $value . '" ';
		
		if (is_array($valueOrArray)) {
			$miscFields = $valueOrArray;
		}

		if (is_array($miscFields)) {
			foreach($miscFields as $k => $v) {
				$str .= $k . '="' . $v . '" ';
			}
		}
		
		if ($valueOrArray == $value && !isset($_REQUEST[$key]) || (isset($_REQUEST[$key]) && $_REQUEST[$key] == $value)) {
			$str .= 'checked="checked" ';
		}
		
		$this->radioIndex++;
		
		$str .= ' />';
		return $str;
	}
	
	protected function processRequestValue($key, $type = "post") {
		$arr = ($type == 'post') ? $_POST : $_GET;
		if (strpos($key, '[') !== false) {
			// we've got something like 'akID[34]['value'] here, which we need to get data from
			if (substr($key, -2) == '[]') {
				$field = preg_replace('/\[/', '][', substr($key, 0, strlen($key)-2), 1);
				if (substr($field, -1) != ']') {
					$field .= ']';
				}
				eval('if (is_array($arr[' . $field . ')) { $v2 = $arr[' . $field . ';}');
			} else {			
				eval('if (isset($arr[' . preg_replace('/\[/', '][', $key, 1) . ')) { $v2 = $arr[' . preg_replace('/\[/', '][', $key, 1) . ';}');
			}

			if (isset($v2)) {
				// if the type if GET, we make sure to strip it of any nasties
				// POST we will let stay unfiltered
				if ($type == 'post') { 
					return $v2;
				} else {
					return preg_replace('/<|>|;|\//','', $v2);
				}
			}
		}			
		if (isset($arr[$key])) {
			// if the type if GET, we make sure to strip it of any nasties
			// POST we will let stay unfiltered
			if ($type == 'post') { 
				return $arr[$key];
			} else {
				return preg_replace('/<|>|;|\//','', $arr[$key]);
			}
		}
		
		return false;
	}
	
	// checks the request based on the key passed. Does things like turn the key into arrays if the key has text versions of [ and ] in it, etc..
	public function getRequestValue($key) {
		$val = $this->processRequestValue($key, 'post');
		if ($val !== false) {
			return $val;
		} else {
			return $this->processRequestValue($key, 'get');
		}
	}

	/**
	 * Renders a text input field. Second argument is either the value of the field (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return $html
	 */
	public function text($key) {
		$a = func_get_args();
		$val = $this->getRequestValue($key);
		$class = "ccm-input-text";

		// index 0 is always the key
		// need to figure out a good way to get a unique ID
		$str = '<input id="' . $key . '" type="text" name="' . $key . '" ';
		
		// if there are two more values, then we treat index 1 as the value in the
		// value field, and index 2 as an assoc. array of other stuff to add
		// to the tag. If there's only one, then it's the array
		if (count($a) == 3) {
			$val = ($val !== false) ? $val : $a[1];
			$str .= 'value="' . $val . '" ';
			$miscFields = $a[2];
			foreach($a[2] as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		} else {
			if (is_array($a[1])) {
				$str .= 'value="' . $val . '" ';
				$miscFields = $a[1];
			} else {
				// we ignore this second value if a post is set with this guy in it
				$val = ($val !== false) ? $val : $a[1];
				$str .= 'value="' . $val . '" ';
			}
		}
		
		if (is_array($miscFields)) {
			foreach($miscFields as $key => $value) {
				if ($key == 'class') {
					$class .= ' ' . $value;
				} else {
					$str .= $key . '="' . $value . '" ';
				}
			}
		}
		$str .= 'class="'.$class.'" />';
		
		return $str;
		
	}


	/**
	 * Renders a select field. First argument is the name of the field. Second is an associative array of key => display. Second argument is either the value of the field to be selected (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return $html
	 */
	public function select($key, $values, $valueOrArray = false, $miscFields = array()) {
		$val = $this->getRequestValue($key);
		if (is_array($val)) {
			$valueOrArray = $val[0];
		}
		
		if ((strpos($key, '[]') + 2) == strlen($key)) {
			$_key = substr($key, 0, strpos($key, '[]'));
			$id = $_key . $this->selectIndex;
		} else {
			$_key = $key;
			$id = $key;
		}


		if (is_array($valueOrArray)) {
			$miscFields = $valueOrArray;
		} else {
			$miscFields['ccm-passed-value'] = $valueOrArray;	
		}
		
		$_class = '';
		if (is_array($miscFields) && isset($miscFields['class'])) {
			$_class = ' ' . $miscFields['class'];
		}
		unset($miscFields['class']);
		$str = '<select class="ccm-input-select' . $_class . '" name="' . $key . '" id="' . $id . '" ';

		if (is_array($miscFields)) {
			foreach($miscFields as $k => $value) {
				$str .= $k . '="' . $value . '" ';
			}
		}
		
		$str .= '>';
		

		foreach($values as $k => $value) { 
			$selected = "";
			if ($valueOrArray == $k && !isset($_REQUEST[$_key]) || ($val !== false && $val == $k) || (is_array($_REQUEST[$_key]) && (in_array($k, $_REQUEST[$_key])))) {
				$selected = 'selected';
			}
			$str .= '<option value="' . $k . '" ' . $selected . '>' . $value . '</option>';
		}

		
		$this->selectIndex++;

		$str .= '</select>';
		return $str;
	}
	


	/**
	 * Renders a password field. Second argument is either the value of the field (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return $html
	 */
	public function password($key) {
		$a = func_get_args();
		$val = $this->getRequestValue($key);
		$class = "ccm-input-password";

		// index 0 is always the key
		// need to figure out a good way to get a unique ID
		$str = '<input id="' . $key . '" type="password" name="' . $key . '" ';
		
		// if there are two more values, then we treat index 1 as the value in the
		// value field, and index 2 as an assoc. array of other stuff to add
		// to the tag. If there's only one, then it's the array
		
		if (count($a) == 3) {
			$val = ($val !== false) ? $val : $a[1];
			$str .= 'value="' . $val . '" ';
			$miscFields = $a[2];
			foreach($a[2] as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		} else {
			if (is_array($a[1])) {
				$str .= 'value="' . $val . '" ';
				$miscFields = $a[1];
			} else {
				// we ignore this second value if a post is set with this guy in it
				$val = ($val !== false) ? $val : $a[1];
				$str .= 'value="' . $val . '" ';
			}
		}
		
		if (is_array($miscFields)) {
			foreach($miscFields as $key => $value) {
				if ($key == 'class') {
					$class .= ' ' . $value;
				} else {
					$str .= $key . '="' . $value . '" ';
				}
			}
		}
		$str .= 'class="'.$class.'" />';
		
		return $str;
		
	}

}

?>
