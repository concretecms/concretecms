<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\LaminasCacheAdapter;
use Concrete\Core\Cache\Level\ExpensiveCache;
use Laminas\Feed\Reader\Feed\FeedInterface;
use Laminas\Feed\Reader\Feed\Rss;
use Laminas\Feed\Reader\Reader;

class FeedService
{
    public function __construct(protected ExpensiveCache $cache)
    {
    }

    /**
     * Loads a newsfeed object.
     *
     * @param string $url
     * @param int $cache - number of seconds to cache the RSS feed data for
     * @return FeedInterface
     */
    public function load(string $url, int $cache = 3600): FeedInterface
    {
        if ($cache !== false) {
            Reader::setCache(new LaminasCacheAdapter($this->cache, ['ttl' => $cache]));
        }

        Reader::setHttpClient(new GuzzleClient());

        // Load the RSS feed, either from remote URL or from cache
        // (if specified above and still fresh)
        return Reader::import($url);
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
