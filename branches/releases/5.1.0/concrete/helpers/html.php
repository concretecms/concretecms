<?php 
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

defined('C5_EXECUTE') or die(_("Access Denied."));
class HtmlHelper {

	/** 
	 * Includes a CSS file in the root /css/ directory of a website. Assumes this directory exists.
	 * @param $file
	 * @return $str
	 */
	public function css($file) {
		// This is only for one-off files. If you have special CSS files that your theme
		// requires then they won't be used in here.
		
		$str = '<style type="text/css">@import "' . DIR_REL . '/' . DIRNAME_CSS . '/' . $file . '.css";</style>';
		return $str;
	}
	
	/** 
	 * Includes a JavaScript file in the root "/js" directory of a website. Assumes the directory exists.
	 * @param string $file
	 * @return string $str
	 */
	public function javascript($file) {
		$str = '<script type="text/javascript" src="' .  DIR_REL . '/' . DIRNAME_JAVASCRIPT . '/' . $file . '.js"></script>';
		return $str;
	}
	
	/** 
	 * Includes an image file when given a src, width and height. Optional attribs array specifies style, other properties.
	 * @todo Make this use getimagesize to generate ?
	 * @param string $src
	 * @param int $width
	 * @param int $height
	 * @param array $attribs
	 * @return string $html
	 */
	public function image($src, $width, $height, $attribs = null) {
		$attribsStr = '';
		if (is_array($attribs)) {
			foreach($attribs as $key => $at) {
				$attribsStr .= " {$key}=\"{$at}\" ";
			}
		}
		$str = '<img src="' . $src . '" width="' . $width . '" border="0" height="' . $height . '" ' . $attribsStr . ' />';
		return $str;
	}	
	
	
}

?>