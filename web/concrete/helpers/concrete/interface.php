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
		return '<input type="submit" class="btn ccm-button-v2 ' . $innerClass . '" value="' . $text . '" id="ccm-submit-' . $formID . '" name="ccm-submit-' . $formID . '" ' . $align . ' ' . $argsstr . ' />';
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
	public function button($text, $href, $buttonAlign = 'right', $innerClass = null, $args = array()) { 
		if ($buttonAlign == 'right') {
			$innerClass .= ' ccm-button-v2-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' ccm-button-v2-left';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<a href="'.$href.'" class="btn '.$innerClass.'" '.$argsstr.'>'.$text.'</a>';
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
		return '<input type="button" class="btn ccm-button-v2 ' . $innerClass . '" value="' . $text . '" onclick="' . $onclick . '" ' . $align . ' ' . $argsstr . ' />';
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
		$html = '<div class="ccm-buttons well">';
		foreach($buttons as $_html) {
			$html .= $_html . ' ';
		}
		$html .= '</div>';
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
	
	public function showWhiteLabelMessage() {
		return ((defined('WHITE_LABEL_LOGO_SRC') && WHITE_LABEL_LOGO_SRC != '')  || file_exists(DIR_BASE . '/' . DIRNAME_IMAGES . '/logo_menu.png'));
	}
	
	public function getToolbarLogoSRC() {
		if (defined('WHITE_LABEL_APP_NAME')) { 
			$alt = WHITE_LABEL_APP_NAME;
		}
		if (!$alt) {
			$alt = 'concrete5';
		}
		if (defined('WHITE_LABEL_LOGO_SRC')) { 
			$src = WHITE_LABEL_LOGO_SRC;
		}
		if (!$src) {
			$filename = 'logo_menu.png';
			if (file_exists(DIR_BASE . '/' . DIRNAME_IMAGES . '/' . $filename)) {
				$src = DIR_REL . '/' . DIRNAME_IMAGES . '/' . $filename;
				$d = getimagesize(DIR_BASE . '/' . DIRNAME_IMAGES . '/' . $filename);
				$dimensions = $d[3];
			} else {
				$src = ASSETS_URL_IMAGES . '/' . $filename;
				$dimensions = 'width="49" height="49"';
			}
		}
		return '<img id="ccm-logo" src="' . $src . '" ' . $dimensions . ' alt="' . $alt . '" title="' . $alt . '" />';
	}
	
	public function showNewsflowOverlay() {
		$tp = new TaskPermission();
		$c = Page::getCurrentPage();
		if (MOBILE_THEME_IS_ACTIVE == false && ENABLE_NEWSFLOW_OVERLAY == true && $tp->canViewNewsflow() && $c->getCollectionPath() != '/dashboard/news') {
			$u = new User();
			$nf = $u->config('NEWSFLOW_LAST_VIEWED');
			if ($nf == 'FIRSTRUN') {
				return false;
			}
			
			if (Config::get('SITE_MAINTENANCE_MODE')) {
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
		
	public function clearInterfaceItemsCache() {
		$u = new User();
		if ($u->isRegistered()) {
			unset($_SESSION['dashboardMenus']);
		}
	}
	
	public function cacheInterfaceItems() {
		$u = new User();
		if ($u->isRegistered()) {
			$ch = Loader::helper('concrete/dashboard');
			$_SESSION['dashboardMenus'][Localization::activeLocale()] = $ch->getDashboardAndSearchMenus();
		}
	}
	
	public function tabs($tabs, $jstabs = true) {
		$tcn = rand(0, getrandmax());

		$html = '<ul class="nav-tabs nav" id="ccm-tabs-' . $tcn . '">';
		foreach($tabs as $t) {
			$dt = $t[0];
			$href = '#';
			if (!$jstabs) {
				$dt = '';
				$href = $t[0];
			}
			$html .= '<li class="' . ((isset($t[2]) && $t[2] == true) ? 'active' : ''). '"><a href="' . $href . '" data-tab="' . $dt . '">' . $t[1] . '</a></li>';
		}
		$html .= '</ul>';
		if ($jstabs) { 
			$html .= '<script type="text/javascript">$(function() { ccm_activateTabBar($(\'#ccm-tabs-' . $tcn . '\'));});</script>';
		}
		return $html;
	}
}