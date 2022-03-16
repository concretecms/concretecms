<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use Concrete\Core\Logging\Channels;
use GuzzleHttp\Client;
use Laminas\Feed\Reader\Reader;
use Laminas\Feed\Reader\Entry\Rss;
use League\Url\Url;
use Laminas\Feed\Reader\Feed\Rss as RssFeed;
use Monolog\Logger;

/**
 * Decorator class around Laminas\Feed\Reader\Entry\Rss that adds sanitization
 */
class FeedPost
{

    /**
     * @var Rss
     */
    protected $post;

    /**
     * @var RssFeed
     */
    protected $feed;

    /**
     * FeedPost constructor.
     * @param Rss $post
     */
    public function __construct(RssFeed $feed, Rss $post)
    {
        $this->feed = $feed;
        $this->post = $post;
    }

    public function __call($method, $args)
    {
        return $this->post->$method(...$args);
    }

    public function getLink()
    {
        $link = $this->post->getLink();
        try {
            $linkParsed = Url::createFromUrl((string) $link);
            if ($linkParsed->getScheme() == 'http' || $linkParsed->getScheme() == 'https') {
                return (string) $linkParsed;
            }
        } catch (\Exception $e) {
            core_log(t('Unable to parse URL from RSS feed: %s', $this->feed->getOriginalSourceUri()), Logger::NOTICE, Channels::CHANNEL_CONTENT);
        }
        return '#';
    }
}
