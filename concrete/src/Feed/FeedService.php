<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use GuzzleHttp\Client;
use Laminas\Feed\Reader\Feed\Rss;
use Laminas\Feed\Reader\Reader;

class FeedService
{
    /**
     * Loads a newsfeed object.
     *
     * @param string $feedurl
     * @param int    $cache - number of seconds to cache the RSS feed data for
     * @return Reader
     */
    public function load($url, $cache = 3600)
    {
        if ($cache !== false) {
            Reader::setCache(new LaminasCacheDriver('cache/expensive', $cache));
        }

        Reader::setHttpClient(new GuzzleClient());

        // Load the RSS feed, either from remote URL or from cache
        // (if specified above and still fresh)
        $feed = Reader::import($url);

        return $feed;
    }

    public function getPosts(Rss $feed): array
    {
        $posts = [];
        foreach ($feed as $post) {
            $posts[] = new FeedPost($feed, $post);
        }
        return $posts;
    }
}
