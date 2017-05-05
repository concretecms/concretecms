<?php
namespace Concrete\Core\Form\Service;

use Core;
use View;

/**
 * Helpful functions for working with forms. Includes HTML input tags and the like.
 *
 * \@package Helpers
 *
 * @category Concrete
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Form
{
    /**
     * Internal counter used to generate unique IDs for radio inputs with the same name.
     *
     * @var int
     */
    protected $radioIndex = 1;

    /**
     * Internal counter used to generate unique IDs for select inputs with the same name.
     *
     * @var int
     */
    protected $selectIndex = 1;

    /**
     * Text helper instance.
     *
     * @var \Concrete\Core\Utility\Service\Text
     */
    protected $th;

    /**
     * Arrays helper instance.
     *
     * @var \Concrete\Core\Utility\Service\Arrays
     */
    protected $ah;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->th = Core::make('helper/text');
        $this->ah = Core::make('helper/arrays');
    }

    /**
     * Returns an action suitable for including in a form action property.
     *
     * @param string $action
     * @param string $task
     */
    public function action($action, $task = null)
    {
        return View::url($action, $task);
    }

    /**
     * Creates a submit button.
     *
     * @param string $key the name/id of the element
     * @param string value The value of the element
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param string $additionalClasses list of additional space-separated CSS class names
     *
     * @return string
     */
    public function submit($key, $value, $miscFields = [], $additionalClasses = '')
    {
        return '<input type="submit"' . $this->parseMiscFields('btn ccm-input-submit ' . $additionalClasses, $miscFields) . ' id="' . $key . '" name="' . $key . '" value="' . $value . '" />';
    }

    /**
     * Creates a button.
     *
     * @param string $key the name/id of the element
     * @param string value The value of the element
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param string $additionalClasses list of additional space-separated CSS class names
     *
     * @return string
     */
    public function button($key, $value, $miscFields = [], $additionalClasses = '')
    {
        return '<input type="button"' . $this->parseMiscFields('btn ccm-input-button ' . $additionalClasses, $miscFields) . ' id="' . $key . '" name="' . $key . '" value="' . $value . '" />';
    }

    /**
     * Creates a label tag.
     *
     * @param string $forFieldID the id of the associated element
     * @param string $innerHTML the inner html of the label
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function label($forFieldID, $innerHTML, $miscFields = [])
    {
        return '<label for="' . $forFieldID . '"' . $this->parseMiscFields('control-label ', $miscFields) . '>' . $innerHTML . '</label>';
    }

    /**
     * Creates a file input element.
     *
     * @param string $key the name/id of the element
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function file($key, $miscFields = [])
    {
        return '<input type="file" id="' . $key . '" name="' . $key . '" value=""' . $this->parseMiscFields('form-control', $miscFields) . ' />';
    }

    /**
     * Creates a hidden form field.
     *
     * @param string $key the name/id of the element
     * @param string $value the value of the element (overriden if we received some data in POST or GET)
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function hidden($key, $value = null, $miscFields = [])
    {
        $requestValue = $this->getRequestValue($key);
        if ($requestValue !== false && (!is_array($requestValue))) {
            $value = $requestValue;
        }

        return '<input type="hidden" id="' . $key . '" name="' . $key . '"' . $this->parseMiscFields('', $miscFields) . ' value="' . $value . '" />';
    }

    /**
     * Generates a checkbox.
     *
     * @param string $key The name/id of the element. It should end with '[]' if it's to return an array on submit.
     * @param string $value String value sent to server, if checkbox is checked, on submit
     * @param string $isChecked "Checked" value (subject to be overridden by $_REQUEST). Checkbox is checked if value is true (string). Note that 'false' (string) evaluates to true (boolean)!
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function checkbox($key, $value, $isChecked = false, $miscFields = [])
    {
        if (substr($key, -2) == '[]') {
            $_field = substr($key, 0, -2);
            $id = $_field . '_' . $value;
        } else {
            $_field = $key;
            $id = $key;
        }

        $checked = false;
        if ($isChecked && \Request::request($_field) === null && !\Request::isPost()) {
            $checked = true;
        } else {
            $requestValue = $this->getRequestValue($key);
            if ($requestValue !== false) {
                if (is_array($requestValue)) {
                    if (in_array($value, $requestValue)) {
                        $checked = true;
                    }
                } elseif ($requestValue == $value) {
                    $checked = true;
                }
            }
        }
        $checked = $checked ? ' checked="checked"' : '';

        return '<input type="checkbox" id="' . $id . '" name="' . $key . '"' . $this->parseMiscFields('ccm-input-checkbox', $miscFields) . ' value="' . $value . '"' . $checked . ' />';
    }

     /**
      * Creates a textarea field.
      *
      * @param string $key the name/id of the element
      * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
      * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
      *
      * @return string
      */
     public function textarea($key, $valueOrMiscFields = '', $miscFields = [])
     {
         if (is_array($valueOrMiscFields)) {
             $value = '';
             $miscFields = $valueOrMiscFields;
         } else {
             $value = $valueOrMiscFields;
         }
         $requestValue = $this->getRequestValue($key);
         if (is_string($requestValue)) {
             $value = $requestValue;
         }

         return '<textarea id="' . $key . '" name="' . $key . '"' . $this->parseMiscFields('form-control', $miscFields) . '>' . $value . '</textarea>';
     }

    /**
     * Generates a radio button.
     *
     * @param string $key the name of the element (its id will start with $key but will have a progressive unique number added)
     * @param string $value the value of the radio button
     * @param string|array $valueOrMiscFields the value of the element (if it should be initially checked) or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function radio($key, $value, $checkedValueOrMiscFields = '', $miscFields = [])
    {
        if (is_array($checkedValueOrMiscFields)) {
            $checkedValue = '';
            $miscFields = $checkedValueOrMiscFields;
        } else {
            $checkedValue = $checkedValueOrMiscFields;
        }
        $checked = false;

        $requestValue = $this->getRequestValue($key);

        if ($requestValue !== false) {
            if ($requestValue == $value) {
                $checked = true;
            }
        } else {
            if ($checkedValue == $value) {
                $checked = true;
            }
        }
        $id = null;
        if (isset($miscFields['id'])) {
            $id = $miscFields['id'];
            unset($miscFields['id']);
        }
        $id = $id ?: $key . $this->radioIndex;
        $str = '<input type="radio" id="' . $id . '" name="' . $key . '" value="' . $value . '"';
        $str .= $this->parseMiscFields('ccm-input-radio', $miscFields);
        if ($checked) {
            $str .= ' checked="checked"';
        }
        $str .= ' />';
        ++$this->radioIndex;

        return $str;
    }

    /**
     * Checks the request based on the key passed.
     * If $key denotes an array (eg akID[34]['value']) we'll turn the key into arrays if the key has text versions of [ and ] in it
     * If the result is a string, it'll be escaped (with htmlspecialchars).
     *
     * @param string $key the name of the field to be checked
     * @param string $type 'post' to check in POST data, other values to check in GET data
     *
     * @return false|array|string returns an array if $key denotes an array and we received that data, a string if $key is the name of a received data, false if $key is not found in the received data
     */
    protected function processRequestValue($key, $type = 'post')
    {
        $arr = ($type == 'post') ? $_POST : $_GET;
        if (strpos($key, '[') !== false) {
            $key = str_replace(']', '', $key);
            $key = explode('[', trim($key, '['));
            $v2 = $this->ah->get($arr, $key);
            if (isset($v2)) {
                if (is_string($v2)) {
                    return $this->th->specialchars($v2);
                } else {
                    return $v2;
                }
            }
        } elseif (isset($arr[$key]) && is_string($arr[$key])) {
            return $this->th->specialchars($arr[$key]);
        }

        return false;
    }

    /**
     * Checks the request (first POST then GET) based on the key passed.
     * If $key denotes an array (eg akID[34]['value']) we'll turn the key into arrays if the key has text versions of [ and ] in it
     * If the result is a string, it'll be escaped (with htmlspecialchars).
     *
     * @param string $key the name of the field to be checked
     * @param string $type 'post' to check in POST data, other values to check in GET data
     *
     * @return false|array|string returns an array if $key denotes an array and we received that data, a string if $key is the name of a received data, false if $key is not found in the received data
     */
    public function getRequestValue($key)
    {
        $result = $this->processRequestValue($key, 'post');
        if ($result === false) {
            $result = $this->processRequestValue($key, 'get');
        }

        return $result;
    }

    /**
     * Internal function that creates an <input> element of type $type. Handles the messiness of evaluating $valueOrMiscFields. Assigns a default class of ccm-input-$type.
     *
     * @param string $key the name/id of the element
     * @param string $type Accepted value for HTML attribute "type"
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    protected function inputType($key, $type, $valueOrMiscFields, $miscFields)
    {
        if (is_array($valueOrMiscFields)) {
            $value = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $value = $valueOrMiscFields;
        }
        $requestValue = $this->getRequestValue($key);
        if (is_string($requestValue)) {
            $value = $requestValue;
        }
        $value = h($value);

        return "<input type=\"$type\" id=\"$key\" name=\"$key\" value=\"$value\"" . $this->parseMiscFields("form-control ccm-input-$type", $miscFields) . ' />';
    }

    /**
     * Renders a text input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function text($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'text', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a number input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function number($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'number', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders an email input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function email($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'email', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a telephone input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function telephone($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'tel', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a URL input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function url($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'url', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a search input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function search($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'search', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a select field.
     *
     * @param string $key The name of the element. If $key denotes an array, the ID will start with $key but will have a progressive unique number added; if $key does not denotes an array, the ID attribute will be $key.
     * @param array $optionValues an associative array of key => display
     * @param string|array $valueOrMiscFields the value of the field to be selected or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return $html
     */
    public function select($key, $optionValues, $valueOrMiscFields = '', $miscFields = [])
    {
        if (!is_array($optionValues)) {
            $optionValues = [];
        }
        if (is_array($valueOrMiscFields)) {
            $selectedValue = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $selectedValue = (string) $valueOrMiscFields;
        }
        if ($selectedValue !== '') {
            $miscFields['ccm-passed-value'] = $selectedValue;
        }
        $requestValue = $this->getRequestValue($key);
        if (is_array($requestValue) && isset($requestValue[0]) && is_string($requestValue[0])) {
            $selectedValue = (string) $requestValue[0];
        } elseif ($requestValue !== false) {
            if (!is_array($requestValue)) {
                $selectedValue = (string) $requestValue;
            } else {
                $selectedValue = '';
            }
        }
        if (substr($key, -2) == '[]') {
            $_key = substr($key, 0, -2);
            $id = $_key . $this->selectIndex;
            ++$this->selectIndex;
        } else {
            $id = $key;
        }
        $str = '<select id="' . $id . '" name="' . $key . '"' . $this->parseMiscFields('form-control', $miscFields) . '>';
        foreach ($optionValues as $k => $text) {
            if (is_array($text)) {
                $str .= '<optgroup label="' . h($k) . '">';
                foreach ($text as $optionValue => $optionText) {
                    $str .= '<option value="' . h($optionValue) . '"';
                    if ((string) $optionValue === (string) $selectedValue) {
                        $str .= ' selected="selected"';
                    }
                    $str .= '>' . h($optionText) . '</option>';
                }
                $str .= '</optgroup>';
            } else {
                $str .= '<option value="' . $k . '"';
                if ((string) $k === (string) $selectedValue) {
                    $str .= ' selected="selected"';
                }
                $str .= '>' . $text . '</option>';
            }
        }
        $str .= '</select>';

        return $str;
    }

    /**
     * Renders a multiple select box.
     *
     * @param string $key The ID of the element. The name attribute will be $key followed by '[].
     * @param array $optionValues Hash array with name/value as the select's option value/text
     * @param array|string $defaultValues Default value(s) which match with the option values; overridden by $_REQUEST
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return $html
     */
    public function selectMultiple($key, $optionValues, $defaultValues = false, $miscFields = [])
    {
        $requestValue = $this->getRequestValue($key . '[]');
        if ($requestValue !== false) {
            $selectedValues = $requestValue;
        } else {
            $selectedValues = $defaultValues;
        }
        if (!is_array($selectedValues)) {
            if (isset($selectedValues) && ($selectedValues !== false)) {
                $selectedValues = (array) $selectedValues;
            } else {
                $selectedValues = [];
            }
        }
        if (!is_array($optionValues)) {
            $optionValues = [];
        }
        $str = "<select id=\"$key\" name=\"{$key}[]\" multiple=\"multiple\"" . $this->parseMiscFields('form-control', $miscFields) . '>';
        foreach ($optionValues as $k => $text) {
            $str .= '<option value="' . $k . '"';
            if (in_array($k, $selectedValues)) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . $text . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /**
     * Renders a password input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function password($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'password', $valueOrMiscFields, $miscFields);
    }

    /**
     * Create an HTML fragment of attribute values, merging any CSS class names as necessary.
     *
     * @param string $defaultClass Default CSS class name
     * @param array $attributes a hash array of attributes (name => value), possibly including 'class'
     *
     * @return string A fragment of attributes suitable to put inside of an HTML tag
     */
    protected function parseMiscFields($defaultClass, $attributes)
    {
        $attributes = (array) $attributes;
        if ($defaultClass) {
            $attributes['class'] = trim((isset($attributes['class']) ? $attributes['class'] : '') . ' ' . $defaultClass);
        }
        $attr = '';
        foreach ($attributes as $k => $v) {
            $attr .= " $k=\"$v\"";
        }

        return $attr;
    }

    /**
     * Generates HTML code that can be added at the beginning of a form to disable username/password autocompletion.
     *
     * @return string
     */
    public function getAutocompletionDisabler()
    {
        $id = str_replace('.', '_', uniqid('ccm_form_autocompletiondisabler_', true));
        $result = <<<EOT
<div id="{$id}" style="position: absolute; top: -1000px; opacity: 0">
    <input type="text" id="{$id}_username" tabindex="-1" />
    <input type="password" id="{$id}_password" tabindex="-1" />
    <script>
    (function() {
        function removeFake() {
            setTimeout(
                function() {
                    var div = document.getElementById('{$id}');
                    div.parentNode.removeChild(div);
                },
                500
            );
        }
        if (window.addEventListener) {
            window.addEventListener('load', removeFake, false);
        } else if (window.attachEvent) {
            window.attachEvent('onload', removeFake);
        }
    })();
    </script>
</div>
EOT;

        return $result;
    }
}
