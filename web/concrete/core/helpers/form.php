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
class Concrete5_Helper_Form {


	private $radioIndex = 1;
	private $selectIndex = 1;

	public function reset() {
		$this->radioIndex = 1;
		$this->selectIndex = 1;
	}
	
	public function __construct() {
		$this->th = Loader::helper("text");
	}

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
		return '<input type="submit"' . $this->parseMiscFields('btn ccm-input-submit ' . $additionalClasses, $fields) . ' id="' . $name . '" name="' . $name . '" value="' . $value . '" />';
	}

	/** 
	 * Creates a button
	 * @param string $name
	 * @param string value
	 * @param array $fields Additional fields appended at the end of the submit button
	 * return string $html
	 */	 
	public function button($name, $value, $fields = array(), $additionalClasses='') {
		return '<input type="button"' . $this->parseMiscFields('btn ccm-input-button ' . $additionalClasses, $fields) . ' id="' . $name . '" name="' . $name . '" value="' . $value . '" />';
	}

	/** 
	 * Creates a label tag
	 * @param string $field
	 * @param string $name
	 * @return string $html
	 */
	public function label($field, $name, $miscFields = array()) {
		$str = '<label for="' . $field . '"' . $this->parseMiscFields('control-label ', $miscFields) . '>' . $name . '</label>';
		return $str;
	}

	/** 
	 * Creates a file input element
	 * @param string $key
	 */
	public function file($key) {
		$str = '<input type="file" id="' . $key . '" name="' . $key . '" value="" class="ccm-input-file" />';
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
	 * Generates a checkbox
	 * @param string $key Checkbox's name and id. Should end with "[]" if it's to return an array on submit.
	 * @param string $value String value sent to server, if checkbox is checked, on submit
	 * @param string $isChecked "Checked" value (subject to be overridden by $_REQUEST). Checkbox is checked if value is true (string). Note that 'false' (string) evaluates to true (boolean)!
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function checkbox($key, $value, $isChecked = false, $miscFields = array()) {
		$id = $key;
		$_field = $key;

		if ((strpos($key, '[]') + 2) == strlen($key)) {
			$_field = substr($key, 0, strpos($key, '[]'));
			$id = $_field . '_' . $value;
		}

		if ($isChecked && (!isset($_REQUEST[$_field])) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$checked = true;
		} else if ($this->getRequestValue($key) == $value) {
			$checked = true;
		} else if (is_array($this->getRequestValue($key)) && in_array($value, $this->getRequestValue($key))) {
			$checked = true;
		}

		if ($checked) {
			$checked = 'checked="checked" ';
		}

		return '<input type="checkbox" '. $this->parseMiscFields('ccm-input-checkbox', $miscFields) . ' name="' . $key . '" id="' . $id . '" value="' . $value . '" ' . $checked . ' />';
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

		$str .= $this->parseMiscFields('ccm-input-textarea', $miscFields);
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
		$str = '<input type="radio" name="' . $key . '" id="' . $key . $this->radioIndex . '" value="' . $value . '" ';

		if (is_array($valueOrArray)) {
			$miscFields = $valueOrArray;
		}

		$str .= $this->parseMiscFields('ccm-input-radio', $miscFields) . ' ';

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
			
			/* @var $ah ArrayHelper */
			$ah = Loader::helper('array');
			$key = str_replace(']', '', $key);
			$key = explode('[', trim($key, '['));
			$v2 = $ah->get($arr, $key);

			if (isset($v2)) {
				// if the type if GET, we make sure to strip it of any nasties
				// POST we will let stay unfiltered
				if (is_string($v2)) {
					return $this->th->entities($v2);
				} else {
					return $v2;
				}
			}
		} else if (isset($arr[$key])) {
			return $this->th->entities($arr[$key]);
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
	 * Internal function that creates an <input> element of type $type. Handles the messiness of evaluating $valueOrArray. Assigns a default class of ccm-input-$type
	 * @param string $key Input element's name and id
	 * @param string $type Accepted value for HTML attribute "type"
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html

	 */
	protected function inputType($key, $type, $valueOrArray, $miscFields) {
		$val = $this->getRequestValue($key);

		if (is_array($valueOrArray)) {
			//valueOrArray is, in fact, the miscFields array
			$miscFields = $valueOrArray;
		} else {
			//valueOrArray is either empty or the default field value; miscFields is either empty or the miscFields
			$val = ($val !== false) ? $val : $valueOrArray;
		}
		$val = str_replace('"', '&#34;', $val);

		return "<input id=\"$key\" type=\"$type\" name=\"$key\" value=\"$val\" " . $this->parseMiscFields("ccm-input-$type", $miscFields) . ' />';
	}

	/**
	 * Renders a text input field.
	 * @param string $key Input element's name and id
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function text($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'text', $valueOrArray, $miscFields);
	}
	
	/**
	* Renders a number input field.
	* @param string $key Input element's name and id
	* @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	* @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	* @return $html
	*/
	public function number($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'number', $valueOrArray, $miscFields);	
	}

	/**
	 * Renders an email input field.
	 * @param string $key Input element's name and id
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function email($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'email', $valueOrArray, $miscFields);
	}
	
	/**
	 * Renders a telephone input field.
	 * @param string $key Input element's name and id
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function telephone($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'tel', $valueOrArray, $miscFields);
	}

	/**
	 * Renders a URL input field.
	 * @param string $key Input element's name and id
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function url($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'url', $valueOrArray, $miscFields);
	}

	/**
	 * Renders a search input field.
	 * @param string $key Input element's name and id
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function search($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'search', $valueOrArray, $miscFields);
	}

	/**
	 * Renders a select field. First argument is the name of the field. Second is an associative array of key => display. Second argument is either the value of the field to be selected (and if it's blank we check post) or a misc. array of fields
	 * @param string $key
	 * @return $html
	 */
	public function select($key, $optionValues, $valueOrArray = false, $miscFields = array()) {
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

		$str = '<select name="' . $key . '" id="' . $id . '" ' . $this->parseMiscFields('ccm-input-select', $miscFields) . '>';

		foreach($optionValues as $k => $value) {
			$selected = "";
			if ((string)$valueOrArray === (string)$k  && !isset($_REQUEST[$_key]) || ($val !== false && $val == $k) || (is_array($_REQUEST[$_key]) && (in_array($k, $_REQUEST[$_key])))) {
				$selected = 'selected="selected"';
			}
			$str .= '<option value="' . $k . '" ' . $selected . '>' . $value . '</option>';
		}

		$this->selectIndex++;

		$str .= '</select>';
		return $str;
	}

	/**
	 * Renders a multiple select box
	 * @param string $key Select's name and id
	 * @param array $optionValues Hash array with name/value as the select's option value/text
	 * @param array|string $defaultValues Default value(s) which match with the option values; overridden by $_REQUEST
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function selectMultiple($key, $optionValues, $defaultValues = false, $miscFields = array()) {
        $val = $this->getRequestValue($key . '[]');

        $defaultValues = (array) $defaultValues;

        if ($val) {
            $defaultValues = $val;
        }

        $str = "<input type='hidden' class='ignore' name='{$key}' value='' />
                <select name=\"{$key}[]\" id=\"$key\" multiple=\"multiple\"" . $this->parseMiscFields('ccm-input-select', $miscFields) . ">";
        foreach ($optionValues as $val => $text) {
            $selected = in_array($val, $defaultValues) ? ' selected="selected"' : '';
            $str .= "<option value=\"$val\"$selected>$text</option>";
        }
        $str .= "</select>";

        return $str;
    }

	/**
	 * Renders a password input field.
	 * @param string $key Input element's name and id
	 * @param string|array $valueOrArray Either the default value (subject to be overridden by $_REQUEST) or $miscFields (see below)
	 * @param array $miscFields A hash array with html attributes as key/value pairs (possibly including "class")
	 * @return $html
	 */
	public function password($key, $valueOrArray = false, $miscFields = array()) {
		return $this->inputType($key, 'password', $valueOrArray, $miscFields);
	}

	/**
	 * Create an HTML fragment of attribute values, merging any CSS class names as necessary
	 * @param string $defaultClass Default CSS class name
	 * @param array $attributes A hash array of attributes (name => value)
	 * @return string a fragment of attributes suitable to put inside of an HTML tag
	 */
	protected function parseMiscFields($defaultClass, $attributes) {
		$attr = '';

		if ($defaultClass) {
			$attributes['class'] = trim((isset($attributes['class']) ? $attributes['class'] : '') . ' ' . $defaultClass);
		}
		
		foreach ((array) $attributes as $k => $v) {
			$attr .= " $k=\"$v\"";
		}

		return $attr;
	}

}
