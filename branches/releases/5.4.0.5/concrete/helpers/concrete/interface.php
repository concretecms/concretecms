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

defined('C5_EXECUTE') or die(_("Access Denied."));
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
	public function submit($text, $formID, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		$onclick = '$(\'#' . $formID . '\').get(0).submit()';
		$href = "javascript:void(0)";
		return $this->generateButton($href, $onclick, $text, $buttonAlign, $innerClass, $args);
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
		return $this->generateButton($href, $onclick, $text, $buttonAlign, $innerClass, $args);
	}

	/** 
	 * Generates a JavaScript function button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $onclick
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function button_js($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		$href = 'javascript:void(0)';
		return $this->generateButton($href, $onclick, $text, $buttonAlign, $innerClass, $args);
	}
	
	protected function generateButton($href, $onclick, $text, $buttonAlign, $innerClass, $args) {
		switch($buttonAlign) {
			case "left";
				$class = 'ccm-button';
				break;
			default: 
				$class = 'ccm-button-right';
				break;
		}
		if (count($args) > 0) {
			$attr = '';
			foreach($args as $key => $val) {
				$attr .= $key . '="' . $val . '" ';
			}
		}
		$html = '<a href="' . $href . '" onclick="' . $onclick . '" class="' . $class . '" ' . $attr . '><span>';
		if ($innerClass != null) {
			$html .= '<em class="' . $innerClass . '">' . $text . '</em>';
		} else {
			$html .= $text;
		}
		$html .= '</span></a>';
		return $html;
	
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