<?
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

	static $menuItems = array();
	
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
			$innerClass .= ' ccm-button-v2-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' ccm-button-v2-left';
		}
		
		if (!$formID) {
			$formID = 'button';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="submit" class="ccm-button-v2 ' . $innerClass . '" value="' . $text . '" id="ccm-submit-' . $formID . '" name="ccm-submit-' . $formID . '" ' . $align . ' ' . $argsstr . ' />';
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
			$innerClass .= ' ccm-button-v2-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' ccm-button-v2-left';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="button" class="ccm-button-v2 ' . $innerClass . '" value="' . $text . '" onclick="window.location.href=\'' . $href . '\'" ' . $align . ' ' . $argsstr . ' />';
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
		if ($buttonAlign == 'right') {
			$innerClass .= ' ccm-button-v2-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' ccm-button-v2-left';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="button" class="ccm-button-v2 ' . $innerClass . '" value="' . $text . '" onclick="' . $onclick . '" ' . $align . ' ' . $argsstr . ' />';
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
	
	public function getQuickNavigationLinkHTML($c) {
		$cnt = Loader::controller($c);
		if (method_exists($cnt, 'getQuickNavigationLinkHTML')) {
			return $cnt->getQuickNavigationLinkHTML();
		} else {
			return '<a href="' . Loader::helper('navigation')->getLinkToCollection($c) . '">' . $c->getCollectionName() . '</a>';
		}
	}
	
	public function showNewsflowOverlay() {
		$tp = new TaskPermission();
		if ($tp->canViewNewsflow()) {
			$u = new User();
			$nf = $u->config('NEWSFLOW_LAST_VIEWED');
			if ($nf == 'FIRSTRUN') {
				return false;
			}
			
			if (!$nf) {
				return true;
			}
			if (time() - $nf > NEWSFLOW_VIEWED_THRESHOLD) {
				return true;
			}
		}
		return false;
	}
	
	public function getQuickNavigationBar() {
		$c = Page::getCurrentPage();
		if (!is_object($c)) {
			return;
		}
		
		if (!is_array($_SESSION['ccmQuickNavRecentPages'])) {
			$_SESSION['ccmQuickNavRecentPages'] = array();
		}
		if (!in_array($c->getCollectionID(), $_SESSION['ccmQuickNavRecentPages'])) {
			$_SESSION['ccmQuickNavRecentPages'][] = $c->getCollectionID();
		}
		if (count($_SESSION['ccmQuickNavRecentPages']) > 5) {
			array_shift($_SESSION['ccmQuickNavRecentPages']);
		}
		
		$html = '';
		$html .= '<div id="ccm-quick-nav">';
		$html .= '<ul id="ccm-quick-nav-favorites" class="pills">';
		$u = new User();
		$quicknav = unserialize($u->config('QUICK_NAV_BOOKMARKS'));
		if (is_array($quicknav)) {
			foreach($quicknav as $cID) {
				$c = Page::getByID($cID);
				$cp = new Permissions($c);
				if ($cp->canRead() && is_object($c) && (!$c->isError())) {
					$html .= '<li id="ccm-quick-nav-page-' . $c->getCollectionID() . '">' . $this->getQuickNavigationLinkHTML($c) . '</li>';
				}
			}
		}
		$html .= '</ul>';
		if (count($_SESSION['ccmQuickNavRecentPages']) > 0) {
			$html .= '<ul id="ccm-quick-nav-breadcrumb" class="pills">';
			$i = 0;
			foreach($_SESSION['ccmQuickNavRecentPages'] as $_cID) {
				$_c = Page::getByID($_cID);
				$name = t('(No Name)');
				$divider = '';
				if (isset($_SESSION['ccmQuickNavRecentPages'][$i+1])) {
					$divider = '<span class="divider">/</span>';
				}
				if ($_c->getCollectionName()) {
					$name = $_c->getCollectionName();
				}
				$html .= '<li><a href="' . Loader::helper('navigation')->getLinkToCollection($_c) . '">' . $name . '</a>' . $divider . '</li>';
				$i++;
			}
			$html .= '</ul>';
		}
		$html .= '</div>';
		return $html;
	}
}