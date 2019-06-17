<?php

namespace Concrete\Core\Express\Search\Index;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Search\Index\Driver\IndexingDriverInterface;
use Concrete\Core\Search\Index\Driver\Iterator;

class EntryIndexer implements IndexingDriverInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function index($entry)
    {
        $entry = $this->objectManager->getEntry($entry);
        if (is_object($entry)) {
            /**
             * @var Entry $entry
             */
            $entity = $entry->getEntity();
            $category = $entity->getAttributeKeyCategory();
            $indexer = $category->getSearchIndexer();
            $values = $category->getAttributeValues($entry);
            foreach ($values as $value) {
                $indexer->indexEntry($category, $value, $entry);
            }
        }
    }

    public function forget($entry)
    {
        return false;
    }


}
