<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Entity\Express\Entity;

/**
 * @since 8.0.0
 */
class ExpressSearchIndexer extends StandardSearchIndexer
{

    public function updateRepository(Entity $previousEntity, Entity $newEntity)
    {
        $previousTable = $previousEntity->getAttributeKeyCategory()->getIndexedSearchTable();
        $newTable = $newEntity->getAttributeKeyCategory()->getIndexedSearchTable();
        if ($this->connection->tableExists($previousTable)) {
            $this->connection->execute(sprintf('alter table %s rename %s', $previousTable, $newTable));
        }

    }
}
