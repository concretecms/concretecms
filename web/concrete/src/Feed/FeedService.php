<?php
namespace Concrete\Core\Feed;

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
        $library = \Cache::getLibrary();
        if ($cache) {
            Reader::setCache($library);
        }
        $feed = Reader::import($url);
        return $feed;
    }

}
