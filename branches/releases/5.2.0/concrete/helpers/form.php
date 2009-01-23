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

defined('C5_EXECUTE') or die(_("Access Denied."));
class FormHelper {


	private $radioIndex = 1;

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
	public function submit($name, $value, $fields = array()) {
		$_fields = '';
		foreach($fields as $key => $fieldvalue) {
			$_fields .= $key . '="' . $fieldvalue . '" ';
		}
		$str = '<input type="submit" class="ccm-input-submit" id="' . $name . '" name="' . $name . '" value="' . $value . '" ' . $_fields . ' />';
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
		$val = '';
		if ($value != null) {
			$val = $value;
		} else if (isset($_REQUEST[$key])) {
			$val = $_REQUEST[$key];
		}
		$str = '<input type="hidden" name="' . $key . '" id="' . $key . '" value="' . $val . '" />';
		return $str;
	}
	
	/** 
	 * Creates an HTML checkbox
	 * @param string $field
	 * @param string $value
	 * @param bool $checked
	 * @return string $html
	 */
	public function checkbox($field, $value, $isChecked = false) {
		
		$_field = $field;
		$_array = false;
		if ((strpos($field, '[]') + 2) == strlen($field)) {
			$_field = substr($field, 0, strpos($field, '[]'));
			$_array = true;
		}

		if ($isChecked && (!isset($_REQUEST[$_field]))) {
			$checked = true;
		} else if ($_REQUEST[$_field] == $value) {
			$checked = true;
		} else if (is_array($_REQUEST[$_field]) && in_array($value, $_REQUEST[$_field])) {
			$checked = true;
		}
			
		if ($checked) {
			$checked = 'checked="checked" ';
		}
		
		$str = '<input type="checkbox" name="' . $field . '" id="' . $field . '" value="' . $value . '" ' . $checked . ' />';
		return $str;
	}

	/** 
	 * Creates a textarea field. Second argument is either the value of the field (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return string $html
	 */
	 public function textarea($key) {
	 	$a = func_get_args();
		$str = '<textarea class="ccm-input-textarea" id="' . $key . '" name="' . $key . '" ';
		if (count($a) == 3) {
			$innerValue = $a[1];
			$miscFields = $a[2];
			foreach($a[2] as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		} else {
			if (is_array($a[1])) {
				$innerValue .= $_REQUEST[$a[0]];
				$miscFields = $a[1];
			} else {
				// we ignore this second value if a post is set with this guy in it
				$innerValue = (isset($_REQUEST[$a[0]])) ? $_REQUEST[$a[0]] : $a[1];
			}
		}
		
		if (is_array($miscFields)) {
			foreach($miscFields as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
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
		
		if (!is_array($isCheckedOrArray)) {
			$isChecked = $isCheckedOrArray;
		} else {
			$miscFields = $isCheckedOrArray;
		}

		if (is_array($miscFields)) {
			foreach($miscFields as $k => $value) {
				$str .= $k . '="' . $value . '" ';
			}
		}
		
		if ($valueOrArray == $value && !isset($_REQUEST[$key]) || (isset($_REQUEST[$key]) && $_REQUEST[$key] == $value)) {
			$str .= 'checked="checked" ';
		}
		
		$this->radioIndex++;
		
		$str .= ' />';
		return $str;
	}
	
	/**
	 * Renders a text input field. Second argument is either the value of the field (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return $html
	 */
	public function text($key) {
		$a = func_get_args();
		// index 0 is always the key
		// need to figure out a good way to get a unique ID
		$str = '<input class="ccm-input-text" id="' . $key . '" type="text" name="' . $key . '" ';
		
		// if there are two more values, then we treat index 1 as the value in the
		// value field, and index 2 as an assoc. array of other stuff to add
		// to the tag. If there's only one, then it's the array
		
		if (count($a) == 3) {
			$str .= 'value="' . $a[1] . '" ';
			$miscFields = $a[2];
			foreach($a[2] as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		} else {
			if (is_array($a[1])) {
				$str .= 'value="' . $_REQUEST[$a[0]] . '" ';
				$miscFields = $a[1];
			} else {
				// we ignore this second value if a post is set with this guy in it
				$val = (isset($_REQUEST[$a[0]])) ? $_REQUEST[$a[0]] : $a[1];
				$str .= 'value="' . $val . '" ';
			}
		}
		
		if (is_array($miscFields)) {
			foreach($miscFields as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		}
		$str .= ' />';
		
		return $str;
		
	}


	/**
	 * Renders a password field. Second argument is either the value of the field (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return $html
	 */
	public function password($key) {
		$a = func_get_args();
		// index 0 is always the key
		// need to figure out a good way to get a unique ID
		$str = '<input class="ccm-input-password" id="' . $key . '" type="password" name="' . $key . '" ';
		
		// if there are two more values, then we treat index 1 as the value in the
		// value field, and index 2 as an assoc. array of other stuff to add
		// to the tag. If there's only one, then it's the array
		
		if (count($a) == 3) {
			$str .= 'value="' . $a[1] . '" ';
			$miscFields = $a[2];
			foreach($a[2] as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		} else {
			if (is_array($a[1])) {
				$str .= 'value="' . $_REQUEST[$a[0]] . '" ';
				$miscFields = $a[1];
			} else {
				// we ignore this second value if a post is set with this guy in it
				$val = (isset($_REQUEST[$a[0]])) ? $_REQUEST[$a[0]] : $a[1];
				$str .= 'value="' . $val . '" ';
			}
		}
		
		if (is_array($miscFields)) {
			foreach($miscFields as $key => $value) {
				$str .= $key . '="' . $value . '" ';
			}
		}
		$str .= ' />';
		
		return $str;
		
	}

}

?>