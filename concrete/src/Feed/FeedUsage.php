<?php

namespace Concrete\Core\Feed;

use Concrete\Core\Entity\Page\Feed as FeedEntity;

class FeedUsage
{
    /**
     * The feed instance.
     *
     * @var \Concrete\Core\Entity\Page\Feed
     */
    protected $feed;

    /**
     * The feed usage entries.
     *
     * @var \Concrete\Core\Feed\FeedUsageEntryInterface[]
     */
    protected $usageEntries = [];

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Entity\Page\Feed $feed the feed instance
     */
    public function __construct(FeedEntity $feed)
    {
        $this->feed = $feed;
    }

    /**
     * Get the feed instance.
     *
     * @return \Concrete\Core\Entity\Page\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Add a new usage entry.
     *
     * @param FeedUsageEntryInterface $entry
     *
     * @return $this
     */
    public function addUsageEntry(FeedUsageEntryInterface $entry)
    {
        $this->usageEntries[] = $entry;

        return $this;
    }

    /**
     * Set the feed usage entries.
     *
     * @param \Concrete\Core\Feed\FeedUsageEntryInterface[] $entries
     *
     * @return $this
     */
    public function setUsageEntries(array $entries)
    {
        $this->usageEntries = $entries;

        return $this;
    }

    /**
     * Get the feed usage entries.
     *
     * @return \Concrete\Core\Feed\FeedUsageEntryInterface[]
     */
    public function getUsageEntries()
    {
        return $this->usageEntries;
    }
}
