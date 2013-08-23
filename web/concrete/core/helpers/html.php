<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions to help with using HTML. Does not include form elements - those have their own helper. 
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Html {
	
	protected $legacyJavascript = array(
		'ccm.dialog.js' => 'ccm.app.js',
		'jquery.metadata.js' => 'ccm.app.js',
		'ccm.themes.js' => 'ccm.app.js',
		'ccm.filemanager.js' => 'ccm.app.js',
		/*'jquery.rating.js' => 'ccm.app.js',*/
		'jquery.colorpicker.js' => 'ccm.app.js',
		'jquery.liveupdate.js' => 'ccm.app.js',
		'ccm.ui.js' => 'ccm.app.js',
		'ccm.search.js' => 'ccm.app.js'
	);
	protected $legacyCSS = array(
		'ccm.dialog.css' => 'ccm.app.css',
		'ccm.ui.css' => 'ccm.app.css',
		'ccm.forms.css' => 'ccm.app.css',
		'ccm.menus.css' => 'ccm.app.css',
		'ccm.search.css' => 'ccm.app.css',
		'ccm.filemanager.css' => 'ccm.app.css',
		'ccm.calendar.css' => 'ccm.app.css'
	);

	/** 
	 * Includes a CSS file. This function looks in several places. 
	 * First, if the item is either a path or a URL it just returns the link to that item (as XHTML-formatted style tag.) 
	 * Then it checks the currently active theme, then if a package is specified it checks there. Otherwise if nothing is found it
	 * fires off a request to the relative directory CSS directory. If nothing is there, then it checks to the assets directories
	 *
	 * @param string $file name of css file
	 * @param string $pkgHandle handle of the package that the css file is located in (if applicable)
	 * @param array $uniqueItemHandle contains two elements: 'handle' and 'version' (both strings) -- helps prevent duplicate output of the same css file (in View::addHeaderItem() and View::addFooterItem()).
	 * @return CSSOutputObject
	 */
	public function css($file, $pkgHandle = null, $uniqueItemHandle = array()) {

		$css = new CSSOutputObject($uniqueItemHandle);

		// if the first character is a / then that means we just go right through, it's a direct path
		if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, DISPATCHER_FILENAME) > -1) {
			$css->compress = false;
			$css->file = $file;
		}
		
		$v = View::getInstance();
		// checking the theme directory for it. It's just in the root.
		if ($v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . $file)) {
			$css->file = $v->getThemePath() . '/' . $file;
		} else if (file_exists(DIR_BASE . '/' . DIRNAME_CSS . '/' . $file)) {
			$css->file = DIR_REL . '/' . DIRNAME_CSS . '/' . $file;
		} else if ($pkgHandle != null) {
			if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
				$css->file = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file;
			} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
				$css->file = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file;
			}
		}
			
		if ($css->file == '') {
			if (isset($this->legacyCSS[$file])) {
				$file = $this->legacyCSS[$file];
			}
			$css->file = ASSETS_URL_CSS . '/' . $file;
		}

		$css->file .= (strpos($css->file, '?') > -1) ? '&amp;' : '?';
		$css->file .= 'v=' . md5(APP_VERSION . PASSWORD_SALT);		
		// for the javascript addHeaderItem we need to have a full href available
		$css->href = $css->file;
		if (substr($css->file, 0, 4) != 'http') {
			$css->href = BASE_URL . $css->file;
		}
		return $css;
	}
	
	/** 
	 * Includes a JavaScript file. This function looks in several places. 
	 * First, if the item is either a path or a URL it just returns the link to that item (as XHTML-formatted script tag.) 
	 * If a package is specified it checks there. Otherwise if nothing is found it
	 * fires off a request to the relative directory JavaScript directory.
	 *
	 * @param string $file name of javascript file
	 * @param string $pkgHandle handle of the package that the javascript file is located in (if applicable)
	 * @param array $uniqueItemHandle contains two elements: 'handle' and 'version' (both strings) -- helps prevent duplicate output of the same javascript file (in View::addHeaderItem() and View::addFooterItem()).
	 * @return JavaScriptOutputObject
	 */
	public function javascript($file, $pkgHandle = null, $uniqueItemHandle = array()) {

		$js = new JavaScriptOutputObject($uniqueItemHandle);
		
		if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, DISPATCHER_FILENAME) > -1) {
			$js->compress = false;
			$js->file = $file;
		}

		if (file_exists(DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
			$js->file = DIR_REL . '/' . DIRNAME_JAVASCRIPT . '/' . $file;
		} else if ($pkgHandle != null) {
			if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
				$js->file = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file;
			} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
				$js->file = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/'. $file;
			}
		}
			
		if ($js->file == '') {
			if (isset($this->legacyJavascript[$file])) {
				$file = $this->legacyJavascript[$file];
			}
			$js->file = ASSETS_URL_JAVASCRIPT . '/' . $file;
		}

		$js->file .= (strpos($js->file, '?') > -1) ? '&amp;' : '?';
		$js->file .= 'v=' . md5(APP_VERSION . PASSWORD_SALT);
		
		// for the javascript addHeaderItem we need to have a full href available
		$js->href = $js->file;
		return $js;
	}
	
	
	/** 
	 * Includes a JavaScript inline script.
	 *
	 * @param string $script javascript code (not including the surrounding <script> tags)
	 * @param array $uniqueItemHandle contains two elements: 'handle' and 'version' (both strings) -- helps prevent duplicate output of the same script (in View::addHeaderItem() and View::addFooterItem()).
	 * @return InlineScriptOutputObject
	 */
	public function script($script, $uniqueItemHandle = array()) {
		$js = new InlineScriptOutputObject($uniqueItemHandle);
		$js->script = $script;
		return $js;
	}
	
	
	/** 
	 * Includes an image file when given a src, width and height. Optional attribs array specifies style, other properties.
	 * First checks the PATH off the root of the site
	 * Then checks the PATH off the images directory at the root of the site.
	 * @param string $src
	 * @param int $width
	 * @param int $height
	 * @param array $attribs
	 * @return string $html
	 */
	public function image($src, $width = false, $height = false, $attribs = null) {
		$image = parse_url($src);
		$attribsStr = '';
		
		if (is_array($width) && $height == false) {
			$attribs = $width;
			$width = false;
		}
		
		if (is_array($attribs)) {
			foreach($attribs as $key => $at) {
				$attribsStr .= " {$key}=\"{$at}\" ";
			}
		}
		
		if ($width == false && $height == false && (!isset($image['scheme']))) {
			// if our file is not local we DON'T do getimagesize() on it. too slow
			$v = View::getInstance();
			if ($v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . DIRNAME_IMAGES . '/' . $src)) {
				$s = getimagesize($v->getThemeDirectory() . '/' . DIRNAME_IMAGES . '/' . $src);
				$width = $s[0];
				$height = $s[1];
				$src = $v->getThemePath() . '/' . DIRNAME_IMAGES . '/' . $src;
			} else if (file_exists(DIR_BASE . '/' . $src)) {
				$s = getimagesize(DIR_BASE . '/' . $src);
				$width = $s[0];
				$height = $s[1];
			} else if (file_exists(DIR_BASE . '/' . DIRNAME_IMAGES . '/' . $src)) {
				$s = getimagesize(DIR_BASE . '/'  . DIRNAME_IMAGES . '/' . $src);
				$width = $s[0];
				$height = $s[1];
				$src = DIR_REL . '/' . DIRNAME_IMAGES . '/' . $src;
			} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_IMAGES . '/' . $src)) {
				$s = getimagesize(DIR_BASE_CORE . '/'  . DIRNAME_IMAGES . '/' . $src);
				$width = $s[0];
				$height = $s[1];
				$src = ASSETS_URL_IMAGES . '/' . $src;
			}
		}
		
		if ($width > 0) {
			$str = '<img src="' . $src . '" width="' . $width . '" border="0" height="' . $height . '" ' . $attribsStr . ' />';
		} else {
			$str = '<img src="' . $src . '" border="0" ' . $attribsStr . ' />';
		}
		return $str;
	}	
	
	
}

class Concrete5_Helper_Html_HeaderOutputObject {

	public $file = '';
	public $href = '';
  	public $script = '';
	public $compress = true;
	public $handle = array(); //optional 'handle' and 'version' that the View class can use to avoid duplicate output of conflicting js/css files
	
	/**
	 * @param optional: pass in handle and version (as array) or just handle (as string; version will be '0')
	 *        	        to avoid duplicate output of the same js/css items from other blocks/theme code.
	 */
	public function __construct($uniqueItemHandle = array()) {
		if (is_array($uniqueItemHandle) && array_key_exists('handle', $uniqueItemHandle) && !empty($uniqueItemHandle['handle'])) {
			$this->handle = array(
				'handle' => $uniqueItemHandle['handle'],
				'version' => array_key_exists('version', $uniqueItemHandle) ? $uniqueItemHandle['version'] : '0',
			);
		} else if (is_string($uniqueItemHandle) && !empty($uniqueItemHandle)) {
			$this->handle = array(
				'handle' => $uniqueItemHandle,
				'version' => '0',
			);
		}
	}

}

class Concrete5_Helper_Html_JavascriptOutputObject extends Concrete5_Helper_Html_HeaderOutputObject {
	public function __toString() {
		return '<script type="text/javascript" src="' . $this->file . '"></script>';
	}
	
}

class Concrete5_Helper_Html_InlinescriptOutputObject extends Concrete5_Helper_Html_HeaderOutputObject {

  public function __toString() {
    return '<script type="text/javascript">/*<![CDATA[*/'. $this->script .'/*]]>*/</script>';
  }
  
}

class Concrete5_Helper_Html_CSSOutputObject extends Concrete5_Helper_Html_HeaderOutputObject {

	public function __toString() {
		return '<link rel="stylesheet" type="text/css" href="' . $this->file . '" />';
	}
	
}
