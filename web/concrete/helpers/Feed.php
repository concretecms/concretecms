<?
namespace Concrete\Helper;
class Feed {

	public function __construct() {
		Loader::library("3rdparty/simplepie");
	}
	
	/**
	 * Loads a newsfeed object.
	 * @param string $feed
	 * @return SimplePie $feed
	 */
	public function load($feedurl, $cache = true) {
		$feed = new SimplePie();
		$feed->set_feed_url($feedurl);
		$feed->set_cache_location(DIR_FILES_CACHE);
		if (!$cache) {
			$feed->enable_cache(false);
		}
		return $feed;
	}
	
	
}