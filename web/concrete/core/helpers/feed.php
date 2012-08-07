<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * A simple wrapper for the 3rd party SimplePie library. Used to parse RSS and ATOM feeds.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Feed {

	public function __construct() {
		Loader::library("3rdparty/simplepie");
	}
	
	/**
	 * Loads a newsfeed object.
	 * @param string $feed
	 * @return SimplePie $feed
	 */
	public function load($feedurl) {
		$feed = new SimplePie();
		$feed->set_feed_url($feedurl);
		$feed->set_cache_location(DIR_FILES_CACHE);
		return $feed;
	}
	
	
}