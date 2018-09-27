<?php

namespace Concrete\Core\Feed;

use Concrete\Core\Cache\Adapter\ZendCacheDriver;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\Feed as FeedEntity;
use PDO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Zend\Feed\Reader\Reader;

class FeedService
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $director;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    public function __construct(EventDispatcherInterface $director, Connection $connection)
    {
        $this->director = $director;
        $this->connection = $connection;
    }

    /**
     * Loads a newsfeed object.
     *
     * @param string $feedurl
     * @param int    $cache - number of seconds to cache the RSS feed data for
     * @param mixed $url
     *
     * @return Reader
     */
    public function load($url, $cache = 3600)
    {
        if ($cache !== false) {
            Reader::setCache(new ZendCacheDriver('cache/expensive', $cache));
        }

        // Load the RSS feed, either from remote URL or from cache
        // (if specified above and still fresh)
        $feed = Reader::import($url);

        return $feed;
    }

    /**
     * Get info about the usage of a feed.
     *
     * @param \Concrete\Core\Entity\Page\Feed $feed
     *
     * @return \Concrete\Core\Feed\FeedUsage
     */
    public function getFeedUsage(FeedEntity $feed)
    {
        $result = new FeedUsage($feed);
        $this->populateFeedUsage($result);
        $this->sortFeedUsage($result);

        return $result;
    }

    /**
     * Populate the usage of a feed.
     *
     * @param \Concrete\Core\Feed\FeedUsage $feedUsage
     */
    protected function populateFeedUsage(FeedUsage $feedUsage)
    {
        $this->populatePageListFeedUsage($feedUsage);
        $this->director->dispatch('populate_feed_usage', new GenericEvent($feedUsage));
    }

    /**
     * Populate the usage of a feed: PageList blocks.
     *
     * @param \Concrete\Core\Feed\FeedUsage $feedUsage
     */
    protected function populatePageListFeedUsage(FeedUsage $feedUsage)
    {
        $rs = $this->connection->executeQuery(
            <<<'EOT'
select distinct CollectionVersions.cID, CollectionVersions.cvID
from btPageList
inner join CollectionVersionBlocks on btPageList.bID = CollectionVersionBlocks.bID
inner join CollectionVersions on CollectionVersionBlocks.cID = CollectionVersions.cID and CollectionVersionBlocks.cvID = CollectionVersions.cvID
where btPageList.pfID = ?
EOT
            ,
            [$feedUsage->getFeed()->getID()]
        );
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $entry = PageListFeedUsageEntry::create($row['cID'], $row['cvID']);
            if ($entry !== null) {
                $feedUsage->addUsageEntry($entry);
            }
        }
    }

    /**
     * Sort the usage of a feed.
     *
     * @param \Concrete\Core\Feed\FeedUsage $feedUsage
     */
    protected function sortFeedUsage(FeedUsage $feedUsage)
    {
        $entries = $feedUsage->getUsageEntries();
        usort($entries, function (FeedUsageEntryInterface $a, FeedUsageEntryInterface $b) {
            return strcmp(get_class($a), get_class($b));
        });
        $feedUsage->setUsageEntries($entries);
    }
}
