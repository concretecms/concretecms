<?php
defined('C5_EXECUTE') or die("Access Denied.");


/**
 * Overrides the default Concrete5 CORE HtmlHelper
 * Giving us a more flexible method of outputting JS and CSS
 * 
 * Copyright (c) 2010 One Hat Design Studio, LLC (www.onehat.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 */
Loader::helper('html');
class Concrete5_Helper_Html_V2 extends HtmlHelper {

	/** 
	 * Concrete5's default behavior expects $file to be a string; so we can send it an array to override the default 
	 * behavior and use our own solution; all the while remaining backwards-compatible with other Concrete5 functionality.
	 * 
	 * $file as an array can contain any of the following optional items:
	 *		$url		// STRING (relative or absolute link to the file)
	 *		$script		// STRING (the actual js code)
	 *		$css		// STRING (the actual css code)
	 *		$media		// STRING (media attribute value for <style> tag)
	 *		$inline		// BOOL 
	 *		$minify		// BOOL (minify the code first?) 
	 *		$IE			// BOOL (surround in IE conditional comments?)
	 *		$IEversion	// STRING (IE's condition to check i.e. 'lte IE 6')
	 *		$fullTag	// STRING (if tag has already been assembled, but needs to be added to document)
	 *		$fixRelativeLinks // BOOL (should we fix relative links inside the CSS? Only works for $inline=true. Defaults to true.
	 * 
	 * Example Usage:
	 * 		$this->addHeaderItems($html->css(array(
	 * 			'url' => 'styles/page_IE.css',
	 * 			'inline' => true,
	 * 			'minify' => true,
	 * 			'IE' => true,
	 * 			'IEversion' => 'lte IE 6'
	 * 		)));
	 */
	public function css($file, $pkgHandle = null) {
		
		if (!is_array($file)) {
			
			// Use default behavior
			return parent::css($file, $pkgHandle);
			
		} else {
			
			// Override the default behavior
			$css = new V2CSSOutputObject();
			
			// create a new variable name so it's not so confusing...
			$args = $file;	
			
			// apply any submitted arguments to the CSSOutputObject
			if (isset($args['url'])) { $css->url = $args['url']; }
			if (isset($args['css'])) { $css->css = $args['css']; }
			if (isset($args['media'])) { $css->media = $args['media']; }
			if (isset($args['inline'])) { $css->inline = $args['inline']; }
			if (isset($args['minify'])) { $css->minify = $args['minify']; }
			if (isset($args['IE'])) { $css->IE = $args['IE']; }
			if (isset($args['IEversion'])) { $css->IEversion = $args['IEversion']; }
			if (isset($args['fullTag'])) { $css->fullTag = $args['fullTag']; }
			if (isset($args['fixRelativeLinks'])) { $css->fixRelativeLinks = $args['fixRelativeLinks']; }
			
			
			// Validate URLs to see if they match a local file
			if ($css->url && !preg_match('/^http:/', $css->url) && !strstr($css->url, '../')) { // for relative links only... and for security reasons, don't allow ../ in the url
				
				// The following code blocks are mostly copied straight from the CORE HtmlHelper
				$v = View::getInstance();
				
				if ($v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . $css->url)) { // checking the theme directory for it. It's just in the root.
					$css->file = $v->getThemePath() . '/' . $css->url;
					$css->rel = $v->getThemePath() . '/';
					$css->abs = $v->getThemeDirectory() . '/' . $css->url;
				} else if ($pkgHandle != null) {
					if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $css->url)) {
						$css->file = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $css->url;
						$css->rel = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/';
						$css->abs = DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $css->url;
					} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $css->url)) {
						$css->file = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $css->url;
						$css->rel = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/';
						$css->abs = DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $css->url;
					}
				}
					
				if ($css->file == '') {
					if (file_exists(DIR_BASE . '/' . DIRNAME_CSS . '/' . $css->url)) {
						$css->file = DIR_REL . '/' . DIRNAME_CSS . '/' . $css->url;
						$css->rel = DIR_REL . '/' . DIRNAME_CSS . '/';
						$css->abs = DIR_BASE . '/' . DIRNAME_CSS . '/' . $css->url;
					} elseif (file_exists(DIR_BASE . '/' . DIRNAME_APP . '/' . DIRNAME_CSS . '/' . $css->url)) { // this works but it's not good to hard-code it in. 
						$css->file = ASSETS_URL_CSS . '/' . $css->url;
						$css->rel = ASSETS_URL_CSS . '/';
						$css->abs = DIR_BASE . '/' . DIRNAME_APP . '/' . DIRNAME_CSS . '/' . $css->url;
					} elseif (file_exists(DIR_BASE . '/' . $css->url)) {
						$css->file = DIR_REL . '/' . $css->url;
						$css->rel = DIR_REL . '/';
						$css->abs = DIR_BASE . '/' . $css->url;
					} else {
						// Can't find the url locally, so just output it as is
					}
				}			
			
			}

			return $css;
			
		}
	}
	
	/** 
	 * Includes a JavaScript file. This function looks in several places. 
	 * First, if the item is either a path or a URL it just returns the link to that item (as XHTML-formatted script tag.) 
	 * If a package is specified it checks there. Otherwise if nothing is found it
	 * fires off a request to the relative directory JavaScript directory.
	 * @param $file
	 * @return $str
	 */
	public function javascript($file, $pkgHandle = null) {

		if (!is_array($file)) {
			
			// Use default behavior
			return parent::javascript($file, $pkgHandle);
			
		} else {
			
			// Override the default behavior
			$js = new V2JavaScriptOutputObject();
			
			// create a new variable name so it's not so confusing...
			$args = $file;	
			
			if (isset($args['url'])) { $js->url = $args['url']; }
			if (isset($args['inline'])) { $js->inline = $args['inline']; }
			if (isset($args['minify'])) { $js->minify = $args['minify']; }
			if (isset($args['script'])) { $js->script = $args['script']; }
			if (isset($args['IE'])) { $js->IE = $args['IE']; }
			if (isset($args['IEversion'])) { $js->IEversion = $args['IEversion']; }
			if (isset($args['fullTag'])) { $js->fullTag = $args['fullTag']; }
			if (isset($args['xhtml'])) { $js->xhtml = $args['xhtml']; }
		
			// Validate URLs to see if they match a local file
			if ($js->url && !preg_match('/^http:/', $js->url) && !strstr($js->url, '../')) { // for relative links only... and for security reasons, don't allow ../ in the url
				
				// The following code blocks are mostly copied straight from the CORE HtmlHelper
				$v = View::getInstance();
				
				if ($v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . $js->url)) {
					$js->file = $v->getThemePath() . '/' . $js->url;
					$js->abs = $v->getThemeDirectory() . '/' . $js->url;
				} else if ($pkgHandle != null) {
					if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url)) {
						$js->file = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
						$js->abs = DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
					} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url)) {
						$js->file = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
						$js->abs = DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
					}
				}
				
				if ($js->file == '') {
					if (file_exists(DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url)) {
						$js->file = DIR_REL . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
						$js->abs = DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
					} elseif (file_exists(DIR_BASE . '/' . DIRNAME_APP . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url)) { // this works but it's not good to hard-code it in. 
						$js->file = ASSETS_URL_JAVASCRIPT . '/' . $js->url;
						$js->abs = DIR_BASE . '/' . DIRNAME_APP . '/' . DIRNAME_JAVASCRIPT . '/' . $js->url;
					} elseif (file_exists(DIR_BASE . '/' . $js->url)) {
						$js->file = DIR_REL . '/' . $js->url;
						$js->abs = DIR_BASE . '/' . $js->url;
					} else {
						// Can't find the url locally, so just output it as is
					}
				}			
			
			}
						
			// this line was added in Concrete v5.4, so we'll add it here for backwards-compatibility... just in case
			$js->href = $js->file;
				
			return $js;
			
		}

	}
	
}

/** 
 * @access private
 */
class V2HeaderOutputObject extends HeaderOutputObject {
	public $string;			// STRING (output of __toString() is saved into this for caching)
	public $url;			// STRING
	public $inline;			// BOOL 
	public $minify = true;	// BOOL (minify the code first?)
	public $IE;				// BOOL (surround in IE conditional comments?)
	public $IEversion;		// STRING
	public $fullTag;		// STRING (if tag has already been assembled, but needs to be added to document)
	public $abs;			// STRING (absolute path to linked file on the server)
}

/** 
 * @access private
 */
class V2JavaScriptOutputObject extends V2HeaderOutputObject {

	public $script;			// STRING (the actual JS code)
	public $xhtml = true;	// BOOL (should we wrap with CDATA...?)

	public function __toString() {
		$fh = Loader::helper('file');
		if (!$this->string) { // If __toString() has not yet been run on this object

			if ($this->inline) { // Put the code inline? Not cacheable by browser!
					
				if (!$this->script && $this->file) {
					$this->script = $fh->getContents($this->abs);
				}
				
				if (!$this->script) { // nothing to insert inline
					return '';
				}
	
				if ($this->minify) {
					Loader::library('3rdparty/jsmin');
					$this->script = JSMin::minify($this->script);
				}
				
				if ($this->xhtml) {
					$this->script = "\n//<![CDATA[\n" . $this->script . "\n//]]>\n";
				}
		
				$html = '<script type="text/javascript">' . $this->script . '</script>';
			
			} elseif ($this->file) {
				
				$html = '<script src="' . $this->file . '" type="text/javascript"></script>';
				
			} elseif ($this->url) {
				
				$html = '<script src="' . $this->url . '" type="text/javascript"></script>';
				
			} elseif ($this->fullTag) {
				
				$html = $this->fullTag;
				
			} else {
				
				return ''; // nothing to insert
				
			}
		
			// Put IE's conditional comments around the tag if requested
			if ($this->IE) {
				$html =  $this->IEversion ? "<!--[if {$this->IEversion}]>" . $html . '<![endif]-->' : '<!--[if IE ]>' . $html . '<![endif]-->';
			}
			
			// Save for caching
			$this->string = $html;
			
		}
	
		return $this->string;
	}
	
}

/** 
 * @access private
 */
class V2CSSOutputObject extends V2HeaderOutputObject {

	public $media = 'all';
	public $fixRelativeLinks = true;
	public $css; // STRING (the actual css code)
	
	public function __toString() {
		$fh = Loader::helper('file');
		if (!$this->string) { // If __toString() has not yet been run on this object
			
			if ($this->inline || $this->css) { // Put the code inline? Not cacheable by browser!	
						
				if (!$this->css && $this->abs) { // get CSS code from the filesystem. Absolute link has already been validated
					$this->css = $fh->getContents($this->abs);
				}
	
				if (!$this->css) { // nothing to insert inline
					return '';
				}
	
				if ($this->fixRelativeLinks && $this->file) {
					$no_filename = preg_replace('/([^\/]*\.css)$/', '', $this->file); // strip off the current filename
					$this->css = preg_replace('/url\((?!http)([\s]*)/', 'url(' . $no_filename, $this->css); // only replace links to local resources
				}
					
				if ($this->minify) {
					$this->css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $this->css); // remove comments
					$this->css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $this->css); // remove tabs, spaces, newlines, etc.
				}
				
				$html = '<style type="text/css" media="' . $this->media . '">' . $this->css . '</style>';
			
			} elseif ($this->file) { // local file. Has already been validated.
				
				$html = '<link href="' . $this->file . '" rel="stylesheet" type="text/css" media="' . $this->media . '" />';
							
			} elseif ($this->url) { // remote file... assume it exists
	
				$html = '<link href="' . $this->url . '" rel="stylesheet" type="text/css" media="' . $this->media . '" />';
	
			} elseif ($this->fullTag) {
	
				$html = $this->fullTag;
	
			} else {
	
				return ''; // nothing to insert
	
			}
			
			// Put IE's conditional comments around the tag if requested
			if ($this->IE) {
				$html =  $this->IEversion ? "<!--[if {$this->IEversion}]>" . $html . '<![endif]-->' : '<!--[if IE ]>' . $html . '<![endif]-->';
			}
		
			// Save for caching
			$this->string = $html;
			
		}
	
		return $this->string;
		
	}	
}