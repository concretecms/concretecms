<?php

namespace Concrete\Core\Feed;

interface FeedUsageEntryInterface
{
    /**
     * Get the heading text to be used before a list of these items.
     *
     * @return string
     */
    public function getHeadingText();

    /**
     * Return an HTML representation of this item.
     *
     * @return string
     */
    public function toHtml();
}
