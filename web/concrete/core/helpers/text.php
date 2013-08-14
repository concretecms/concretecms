<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for working with text.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Text { 
	
	/** 
	 * @access private
	 * @param string $handle
	 * @return string $handle
	 */
	public function sanitizeFileSystem($handle) {
		return $this->urlify($handle);
	}	
	
	/** 
	 * Takes text and returns it in the "lowercase-and-dashed-with-no-punctuation" format
	 * @param string $handle
	 * @param int $maxlength Max number of characters of the return value
	 * @param string $lang Language code of the language rules that should be priorized
	 * @return string $handle
	 */
	public function urlify($handle, $maxlength = PAGE_PATH_SEGMENT_MAX_LENGTH, $lang = LANGUAGE) {
		Loader::library('3rdparty/urlify');
		$handle = URLify::filter($handle, $maxlength, $lang);
		return $handle;
	}
		

	/** 
	 * Strips tags and optionally reduces string to specified length.
	 * @param string $string
	 * @param int $maxlength
	 * @param string $allowed
	 * @return string
	 */
	function sanitize($string, $maxlength = 0, $allowed = '') {	
            $text = trim(strip_tags($string, $allowed));
		if ($maxlength > 0) {
			if (function_exists('mb_substr')) {
				$text = mb_substr($text, 0, $maxlength, APP_CHARSET);
			} else {
				$text = substr($text, 0, $maxlength);
			}
		}
		if ($text == null) {
			return ""; // we need to explicitly return a string otherwise some DB functions might insert this as a ZERO.
		}
		return $text;
	}

	/**
	 * Leaves only characters that are valid in email addresses (RFC)
	 * @param string $email
	 * @return string
	 */
	public function email($email) {
		$regex = "/[^a-zA-Z0-9_\.!#\$\&'\*\+-?^`{|}~@]/i";
		return preg_replace($regex, '', $email);
	}

	/** 
	 * Leaves only characters that are alpha-numeric
	 * @param string $text
	 * @return string
	 */
	public function alphanum($string) {
		return preg_replace('/[^A-Za-z0-9]/', '', $string);
	}
	
	/**
	 * always use in place of htmlentites(), so it works with different langugages
	 * @param string $v
	 * @return string
	 */
	public function entities($v){
		return htmlentities( $v, ENT_COMPAT, APP_CHARSET); 
	}
	
	/** Decodes html-encoded entities (for instance: from '&gt;' to '>')
	* @param string $v
	* @return string
	*/
	public function decodeEntities($v) {
		return html_entity_decode($v, ENT_QUOTES, APP_CHARSET);
	}

	/** 
	 * A concrete5 specific version of htmlspecialchars(). Double encoding is OFF, and the character set is set to your site's.
	 */
	public function specialchars($v) {
		return htmlspecialchars($v, ENT_COMPAT, APP_CHARSET, false);
	}
	 
	 
	/**
	 * An alias for shorten()
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 * @return string
	 */
	public function shorten($textStr, $numChars = 255, $tail = '…') {
		return $this->shortText($textStr, $numChars, $tail);
	}
	
	/** 
	 * Like sanitize, but requiring a certain number characters, and assuming a tail
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 * @return string $textStr
	 */	
	function shortText($textStr, $numChars=255, $tail='…') {
		if (intval($numChars)==0) $numChars=255;
		$textStr=strip_tags($textStr);
		if (function_exists('mb_substr') && function_exists('mb_strlen')) {
			if (mb_strlen($textStr, APP_CHARSET) > $numChars) { 
				$textStr = mb_substr($textStr, 0, $numChars, APP_CHARSET) . $tail;
			}
		} else {
			if (strlen($textStr) > $numChars) { 
				$textStr = substr($textStr, 0, $numChars) . $tail;
			}
		}
		return $textStr;			
	}
        
	/**
	* Shortens and sanitizes a string but only cuts at word boundaries
	* @param string $textStr
	* @param int $numChars
	* @param string $tail
	*/
	function shortenTextWord($textStr, $numChars=255, $tail='…') {
		if (intval($numChars)==0) $numChars=255;
		$textStr=strip_tags($textStr);
		if (function_exists('mb_substr')) {
			if (mb_strlen($textStr, APP_CHARSET) > $numChars) { 
				$textStr=preg_replace('/\s+?(\S+)?$/', '', mb_substr($textStr, 0, $numChars + 1, APP_CHARSET));
				// this is needed if the shortened string consists of one single word
				$textStr = mb_substr($textStr, 0, $numChars, APP_CHARSET). $tail;
			}
		} else {
			if (strlen($textStr) > $numChars) { 
				$textStr = preg_replace('/\s+?(\S+)?$/', '', substr($textStr, 0, $numChars + 1));
				$textStr = substr($textStr, 0, $numChars). $tail;
			}
		}
		return $textStr;
	}

	
	
	/**
	 * Takes a string and turns it into the CamelCase or StudlyCaps version
	 * @param string $string
	 * @return string
	 */
	public function camelcase($string) {
		return Object::camelcase($string);
	}
	
	/** 
	 * Scans passed text and automatically hyperlinks any URL inside it
	 * @param string $input
	 * @param int $newWindow
	 * @return string $output
	 */
	public function autolink($input,$newWindow=0) {
		$target=($newWindow)?' target="_blank" ':'';
		$output = preg_replace("/(http:\/\/|https:\/\/|(www\.))(([^\s<]{4,80})[^\s<]*)/", '<a href="http://$2$3" '.$target.' rel="nofollow">http://$2$4</a>', $input);
		return ($output);
	}
	
	/** 
	 * automatically add hyperlinks to any twitter style @usernames in a string
	 * @param string $input
	 * @param int $newWindow
	 * @param int $withSearch
	 * @return string $output
	 */	
	public function twitterAutolink($input,$newWindow=0,$withSearch=0) {
		$target=($newWindow)?' target="_blank" ':'';
    	$output = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" ".$target." class=\"twitter-username\">@$2</a>$3 ", $input);
		if($withSearch) 
			$output = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://search.twitter.com/search?q=%23$2\" ".$target." class=\"twitter-search\">#$2</a>$3 ", $input);		
    	return $output;
	}  
	
	/**
	 * Runs a number of text functions, including autolink, nl2br, strip_tags. Assumes that you want simple
	 * text comments but with a few niceties.
	 * @param string $input
	 * @return string $output
	 */
	public function makenice($input) {
		$output = strip_tags($input);
		$output = $this->autolink($output);
		$output = nl2br($output);
		return $output;
	}
	
	/** 
	 * A wrapper for PHP's fnmatch() function, which some installations don't have.
	 * @param string $pattern
	 * @param string $string
	 * @return bool
	 */
	public function fnmatch($pattern, $string) {
		if(!function_exists('fnmatch')) {
			return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'))."$#i", $string);
		} else {
			return fnmatch($pattern, $string);
		}
	}
	
	
	/** 
	 * Takes a CamelCase string and turns it into camel_case
	 * @param string $string
	 * @return string
	 */
	public function uncamelcase($string) {
		return Object::uncamelcase($string);
	}
	
	/**
	 * Takes a handle-based string like "blah_blah" or "blah-blah" or "blah/blah" and turns it into "Blah Blah"
	 * @param string $string
	 * @return string $r1
	 */
	public function unhandle($string) {
		$r1 = ucwords(str_replace(array('_', '-', '/'), ' ', $string));
		return $r1;
	}

	/** 
	 * Takes a string and turns it into a handle.
	 */
	public function handle($handle, $leaveSlashes=false) {
		return $this->sanitizeFileSystem($handle, $leaveSlashes=false);
	}
	
	/**
	 * alias of shortenTextWord()
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 * @return string
	 */
	public function wordSafeShortText($textStr, $numChars=255, $tail='…') {
		return $this->shortenTextWord($textStr, $numChars, $tail);
	}

	
	/**
	 * Strips out non-alpha-numeric characters
	 * @param string $val
	 * @return string
	 */
	public function filterNonAlphaNum($val){ 
		return preg_replace('/[^[:alnum:]]/', '', $val);
	}
	
	/** 
	 * Highlights a string within a string with the class ccm-highlight-search
	 * @param string $value
	 * @param string $searchString
	 * @return string
	 */
	 
	public function highlightSearch($value, $searchString) {
		if (strlen($value) < 1 || strlen($searchString) < 1) {
		    return $value;
		}
		preg_match_all("/$searchString+/i", $value, $matches);
		if (is_array($matches[0]) && count($matches[0]) > 0) {
			return str_replace($matches[0][0], '<em class="ccm-highlight-search">'.$matches[0][0].'</em>', $value);
		}
		return $value;
	}
	
	/** 
	 * Formats a passed XML string nicely
	 * @param string $xml
	 */
	public function formatXML($xml) {  
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($xml);
		$dom->formatOutput = true;
		return $dom->saveXml();
	}

	/** 
	 * Appends a SimpleXMLElement to a SimpleXMLElement
	 */
	public function appendXML($root, $new) {
		$node = $root->addChild($new->getName(), (string) $new);
		foreach($new->attributes() as $attr => $value) {
			$node->addAttribute($attr, $value);
		}
		foreach($new->children() as $ch) {
			$this->appendXML($node, $ch);
		}
	}
	
}
