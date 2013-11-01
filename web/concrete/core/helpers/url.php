<?
/**
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Url { 

	public function setVariable($variable, $value = false, $url = false) {
		// either it's key/value as variables, or it's an associative array of key/values
		
		if ($url == false) {
			$url = Loader::helper('security')->sanitizeString($_SERVER['REQUEST_URI']);
		} elseif(!strstr($url,'?')) {
			$url = $url . '?' . Loader::helper('security')->sanitizeString($_SERVER['QUERY_STRING']);
		}

 		$vars = array();
		if (!is_array($variable)) {
			$vars[$variable] = $value;
		} else {
			$vars = $variable;
		}
		
		foreach($vars as $variable => $value) {
			$url = preg_replace('/(.*)(\?|&)' . $variable . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
			$url = substr($url, 0, -1);
			if (strpos($url, '?') === false) {
				$url = $url . '?' . $variable . '=' . $value;
			} else {
				$url = $url . '&' . $variable . '=' . $value;
			}
		}
		
		// THIS DOES NOT WORK. SOMEONE WILL NEED TO FIX THIS PROPERLY IF THE W3C FOLKS WANT IT TO WORK
		//$url = str_replace('&', '&amp;', $url);
		return $url;
	}
	
	public function unsetVariable($variable, $url = false) {
		// either it's key/value as variables, or it's an associative array of key/values
		
		if ($url == false) {
			$url = $_SERVER['REQUEST_URI'];
		} elseif(!strstr($url,'?')) {
			$url = $url . '?' . $_SERVER['QUERY_STRING'];
		}

 		$vars = array();
		if (!is_array($variable)) {
			$vars[] = $variable;
		} else {
			$vars = $variable;
		}
		
		foreach($vars as $variable) {
		  $url = preg_replace('/(.*)(\?|&)' . $variable . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
		  $url = substr($url, 0, -1); 
		}
		
		
		// THIS DOES NOT WORK. SOMEONE WILL NEED TO FIX THIS PROPERLY IF THE W3C FOLKS WANT IT TO WORK
		//$url = str_replace('&', '&amp;', $url);
		return $url;
	}	
	public function buildQuery($url, $params) {
		return $url . '?' . http_build_query($params, '', '&');
	}

    /**
	 * Shortens a given url with the tiny url api
	 * @param string $strURL
	 * @return string $url
	 */
	public function shortenURL($strURL) {
		$file = Loader::helper('file');
		$url = $file->getContents("http://tinyurl.com/api-create.php?url=".$strURL);
		return $url;
	}

	
}