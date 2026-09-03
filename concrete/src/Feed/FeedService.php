<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Client\Factory as HttpClientFactory;
use Concrete\Core\Url\Validation\InvalidRemoteUrlException;
use Concrete\Core\Url\Validation\RemoteUrlRequestOptionsBuilder;
use Concrete\Core\Url\Validation\RemoteUrlValidator;
use Concrete\Core\Url\Validation\ValidatedRemoteUrl;
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

    /**
     * @var \Concrete\Core\Url\Validation\RemoteUrlValidator
     */
    protected $remoteUrlValidator;

    /**
     * @var \Concrete\Core\Url\Validation\RemoteUrlRequestOptionsBuilder
     */
    protected $remoteUrlRequestOptionsBuilder;

    public function __construct(Repository $config, HttpClientFactory $httpClientFactory)
    {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
        $this->remoteUrlValidator = new RemoteUrlValidator();
        $this->remoteUrlRequestOptionsBuilder = new RemoteUrlRequestOptionsBuilder();
    }

    /**
     * Loads a newsfeed object.
     *
     * @param string $feedurl
     * @param int    $cache - number of seconds to cache the RSS feed data for
     * @return \Laminas\Feed\Reader\Feed\FeedInterface
     */
    public function load($url, $cache = 3600)
    {
        if ($cache !== false) {
            Reader::setCache(new LaminasCacheDriver('cache/expensive', $cache));
        }

        $validatedUrl = $this->getValidatedFeedUrl($url);
        Reader::setHttpClient(new GuzzleClient($this->buildHttpClient($validatedUrl)));

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
    protected function buildHttpClient(ValidatedRemoteUrl $validatedUrl)
    {
        $options = [
            'timeout' => 5,
        ] + $this->httpClientFactory->getDefaultOptions($this->config);
        $options = array_replace($options, $this->remoteUrlRequestOptionsBuilder->build($validatedUrl));

        return $this->httpClientFactory->createFromOptions($options);
    }

    protected function getValidatedFeedUrl($url)
    {
        try {
            return $this->remoteUrlValidator->validate((string) $url);
        } catch (InvalidRemoteUrlException $x) {
            throw new UserMessageException(t('The RSS feed URL is not valid.'));
        }
    }
}
