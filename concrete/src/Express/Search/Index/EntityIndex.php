<?php

namespace Concrete\Core\Express\Search\Index;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Search\Index\AbstractIndex;
use Concrete\Core\Search\Index\Driver\IndexingDriverInterface;
use Doctrine\DBAL\Connection;

class EntityIndex extends AbstractIndex implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection, Entity $entity)
    {
        $this->connection = $connection;
        $this->entity = $entity;
    }

    /**
     * @return IndexingDriverInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexDriver) {
            $this->indexDriver = $this->app->make(EntryIndexer::class);
        }

        return $this->indexDriver;
    }

    /**
     * Clear out all indexed items
     * @return void
     */
    public function clear()
    {
        $category = $this->entity->getAttributeKeyCategory();
        $table = $category->getIndexedSearchTable();

        /** @var ExpressKey $key */
        if (!$this->connection->tableExists($table)) {
            $indexer = $category->getSearchIndexer();
            $indexer->createRepository($category);
        }

        foreach ($category->getList() as $key) {
            /** @var SearchIndexerInterface $indexer */
            $indexer = $key->getSearchIndexer();

            // Update the key tables
            $indexer->updateSearchIndexKeyColumns($category, $key);
        }


        // Truncate the existing search index
        if ($table) {
            $this->connection->Execute(sprintf('truncate table %s', $table));
        }
    }

}
