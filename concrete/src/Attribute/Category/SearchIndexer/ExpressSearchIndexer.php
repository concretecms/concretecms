<?php

namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Entity\Express\Entity;

class ExpressSearchIndexer extends StandardSearchIndexer
{
    /**
     * Update (if needed) the name of the index table ater the express entity changed (for example because its handle changed).
     *
     * @param \Concrete\Core\Entity\Express\Entity $previousEntity
     * @param \Concrete\Core\Entity\Express\Entity $newEntity
     */
    public function updateRepository(Entity $previousEntity, Entity $newEntity)
    {
        $previousTable = $previousEntity->getAttributeKeyCategory()->getIndexedSearchTable();
        $newTable = $newEntity->getAttributeKeyCategory()->getIndexedSearchTable();
        if ($this->connection->tableExists($previousTable)) {
            $this->connection->execute(sprintf('alter table %s rename %s', $previousTable, $newTable));
        }
    }
}
