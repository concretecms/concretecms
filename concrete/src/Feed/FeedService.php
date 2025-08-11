<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Client\Factory as HttpClientFactory;
use Laminas\Feed\Reader\Feed\FeedInterface;
use Laminas\Feed\Reader\Reader;

class FeedService
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;
    
    /**
     * @var \Concrete\Core\Http\Client\Factory
     */
    protected $httpClientFactory;

    public function __construct(Repository $config, HttpClientFactory $httpClientFactory)
    {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
    }

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

        Reader::setHttpClient(new GuzzleClient($this->buildHttpClient()));

        // Load the RSS feed, either from remote URL or from cache
        // (if specified above and still fresh)
        $feed = Reader::import($url);

        return $feed;
    }

    public function getPosts(FeedInterface $feed): array
    {
        $posts = [];
        foreach ($feed as $post) {
            $posts[] = new FeedPost($feed, $post);
        }
        return $posts;
    }

    /**
     * @return \Concrete\Core\Http\Client\Client
     */
    protected function buildHttpClient()
    {
        $options = [
            'timeout' => 5,
        ] + $this->httpClientFactory->getDefaultOptions($this->config);

        return $this->httpClientFactory->createFromOptions($options);
    }
}
