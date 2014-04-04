<?
namespace Concrete\Helper;
use Concrete\Core\Asset\CSSAsset;
use Concrete\Core\Asset\JavaScriptAsset;
use Concrete\Core\View\View;

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

class Html {
	
	public function css($file, $pkgHandle = null) {
		$asset = new CSSAsset();		
		// if the first character is a / then that means we just go right through, it's a direct path
		if (substr($file, 0, 4) == 'http' || substr($file, 0, 2) == '//') {
			$asset->setAssetURL($file);
			$asset->setAssetIsLocal(false);
		} else if (substr($file, 0, 1) == '/') {
			$asset->setAssetURL($file);
			$asset->setAssetPath(DIR_BASE . $file);
		} else {
			$v = View::getInstance();
			// checking the theme directory for it. It's just in the root.
			if ($v instanceof View && $v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . $file)) {
				$asset->setAssetURL($v->getThemePath() . '/' . $file);
				$asset->setAssetPath($v->getThemeDirectory() . '/' . $file);
			} else if (file_exists(DIR_BASE . '/' . DIRNAME_CSS . '/' . $file)) {
				$asset->setAssetURL(DIR_REL . '/' . DIRNAME_CSS . '/' . $file);
				$asset->setAssetPath(DIR_BASE . '/' . DIRNAME_CSS . '/' . $file);
			} else if ($pkgHandle != null) {
				if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
					$asset->setAssetURL(DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file);
					$asset->setAssetPath(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file);
				} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
					$asset->setAssetURL(ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file);
					$asset->setAssetPath(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file);
				}
			}
		}

		if (!$asset->getAssetURL()) {
			$asset->setAssetURL(ASSETS_URL_CSS . '/' . $file);
			$asset->setAssetPath(DIR_BASE_CORE . '/' . DIRNAME_CSS . '/' . $file);	
		}
		return $asset;
	}

	public function javascript($file, $pkgHandle = null) {
		$asset = new JavaScriptAsset();		
		// if the first character is a / then that means we just go right through, it's a direct path
		if (substr($file, 0, 4) == 'http' || substr($file, 0, 2) == '//') {
			$asset->setAssetURL($file);
			$asset->setAssetIsLocal(false);
		} else if (substr($file, 0, 1) == '/') {
			$asset->setAssetURL($file);
			$asset->setAssetPath(DIR_BASE . $file);
		} else {
			if (file_exists(DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
				$asset->setAssetURL(DIR_REL . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
				$asset->setAssetPath(DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
			} else if ($pkgHandle != null) {
				if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
					$asset->setAssetURL(DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
					$asset->setAssetPath(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
				} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
					$asset->setAssetURL(ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
					$asset->setAssetPath(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
				}
			}
		}

		if (!$asset->getAssetURL()) {
			$asset->setAssetURL(ASSETS_URL_JAVASCRIPT . '/' . $file);
			$asset->setAssetPath(DIR_BASE_CORE . '/' . DIRNAME_JAVASCRIPT . '/' . $file);	
		}
		return $asset;
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
			if ($v instanceof View && $v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . DIRNAME_IMAGES . '/' . $src)) {
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
