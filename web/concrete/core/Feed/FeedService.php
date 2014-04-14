<?
namespace Concrete\Core\Feed;
class FeedService {
	
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