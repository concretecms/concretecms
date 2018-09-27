<?php

namespace Concrete\Core\Feed;

class PageListFeedUsageEntry extends FeedUsageOnPageEntry
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Feed\FeedUsageEntryInterface::getHeadingText()
     */
    public function getHeadingText()
    {
        return t('The RSS feed is used by blocks of type %s in the following places:', t('Page List'));
    }
}
