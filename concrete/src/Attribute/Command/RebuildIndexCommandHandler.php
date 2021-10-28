<?php

namespace Concrete\Core\Attribute\Command;

use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;

class RebuildIndexCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * RebuildIndexCommandHandler constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function __invoke(RebuildIndexCommandInterface $command)
    {
        $this->output->write(t("Rebuilding index table for '%s'...", $command->getIndexName()));
        $category = $command->getAttributeKeyCategory();
        $table = $category->getIndexedSearchTable();

        /** @var ExpressKey $key */
        if (!$this->connection->tableExists($table)) {
            $indexer = $category->getSearchIndexer();
            $indexer->createRepository($category);
        }

        foreach ($category->getList() as $key) {
            /** @var SearchIndexerInterface $indexer */
            $indexer = $key->getSearchIndexer();

            $this->output->write(t("Adding key '%s' to search index table '%s'...", $key->getAttributeKeyHandle(), $table));

            // Update the key tables
            $indexer->updateSearchIndexKeyColumns($category, $key);
        }
    }

}