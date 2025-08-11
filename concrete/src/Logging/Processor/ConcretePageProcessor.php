<?php

namespace Concrete\Core\Logging\Processor;

use Concrete\Core\Page\Page;

/**
 * A processor for adding the Concrete page into the extra log info
 */
class ConcretePageProcessor
{

    /**
     * Invoke this processor
     *
     * @param array $record The given monolog record
     *
     * @return array The modified record
     */
    public function __invoke(array $record)
    {
        $page = Page::getCurrentPage();
        if ($page && !$page->isError()) {
            $record['extra']['page'] = [$page->getCollectionID(), $page->getCollectionName()];
        }
        return $record;
    }

}


