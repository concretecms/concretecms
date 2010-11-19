<?php 
/**
 * @package Helpers
 * @subpackage Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Useful functions for generating elements on the Concrete interface
 * @subpackage Concrete
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceHelper {

	
	/** 
	 * Generates a submit button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $formID The form this button will submit
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function submit($text, $formID = false, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		if ($buttonAlign == 'right') {
			$align = 'style="float: right"';
		}
		if (!$formID) {
			$formID = 'button';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="submit" class="ccm-button-v2" value="' . $text . '" id="ccm-submit-' . $formID . '" name="ccm-submit-' . $formID . '" ' . $align . ' ' . $argsstr . ' />';
	}
	
	/** 
	 * Generates a simple link button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $href
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function button($text, $href, $buttonAlign = 'right', $innerClass = null, $args = array(), $onclick='') { 
		if ($buttonAlign == 'right') {
			$align = 'style="float: right"';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="button" class="ccm-button-v2" value="' . $text . '" onclick="window.location.href=\'' . $href . '\'" ' . $align . ' ' . $argsstr . ' />';
	}

	/** 
	 * Generates a JavaScript function button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $onclick
	 * @param string $buttonAlign
	 * @param string $innerClass - no longer used
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function button_js($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		$href = 'javascript:void(0)';
		if ($buttonAlign == 'right') {
			$align = 'style="float: right"';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="button" class="ccm-button-v2" value="' . $text . '" onclick="' . $onclick . '" ' . $align . ' ' . $argsstr . ' />';
	}
	
	/** 
	 * Outputs button text passed as arguments with a special Concrete wrapper for positioning
	 * <code>
	 *    $bh->buttons($myButton1, $myButton2, $myButton3);
	 * </code>
	 * @param string $buttonHTML
	 * @return string
	 */	
	public function buttons($buttons = null) {
		if (!is_array($buttons)) {
			$buttons = func_get_args();
		}
		$html = '<div class="ccm-buttons">';
		foreach($buttons as $_html) {
			$html .= $_html;
		}
		$html .= '</div><div class="ccm-spacer">&nbsp;</div>';
		return $html;
	}	
}