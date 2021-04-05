<?php

namespace Concrete\Core\Page\Search\Index;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Search\Index\AbstractIndex;
use Concrete\Core\Search\Index\Driver\IndexingDriverInterface;
use Concrete\Core\Search\Index\Driver\SearchingDriverInterface;

class PageIndex extends AbstractIndex implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * PageIndex constructor.
     * Doesn't require any constructor arguments
     */
    public function __construct()
    {
    }

    /**
     * @return DefaultPageDriver|IndexingDriverInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexDriver) {
            $this->indexDriver = $this->app->make(PageIndexer::class);
        }

        return $this->indexDriver;
    }

    /**
     * Clear out all indexed items
     * @return void
     */
    public function clear()
    {
        // Truncate the existing search index
        $database = $this->app['database']->connection();
        if ($database->tableExists('PageSearchIndex')) {
            $database->Execute('truncate table PageSearchIndex');
        }
        if ($database->tableExists('CollectionSearchIndexAttributes')) {
            $database->Execute('truncate table CollectionSearchIndexAttributes');
        }
    }

}
