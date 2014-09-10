<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\ZendCacheDriver;
use \Zend\Feed\Reader\Reader;

class FeedService
{

    /**
     * Loads a newsfeed object.
     *
     * @param string $feedurl
     * @param bool   $cache
     * @return Reader
     */
    public function load($url, $cache = true)
    {
        if ($cache) {
            Reader::setCache(new ZendCacheDriver());
        }
        $feed = Reader::import($url);
        return $feed;
    }

}
