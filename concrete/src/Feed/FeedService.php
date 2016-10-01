<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\ZendCacheDriver;
use Zend\Feed\Reader\Reader;
use Concrete\Core\Support\Facade\Application;

class FeedService
{
    /**
     * Loads a newsfeed object.
     *
     * @param string $feedurl
     * @param int    $cache - number of seconds to cache the RSS feed data for
     *
     * @return Reader
     */
    public function load($url, $cache = 3600)
    {
        if ($cache !== false) {
            $app = Application::getFacadeApplication();
            $driver = $app->make(ZendCacheDriver::class, ['cache/expensive', $cache]);
            Reader::setCache($driver);
        }

        // Load the RSS feed, either from remote URL or from cache
        // (if specified above and still fresh)
        $feed = Reader::import($url);

        return $feed;
    }
}
