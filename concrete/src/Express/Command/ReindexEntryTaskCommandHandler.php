<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\ObjectManager;

class ReindexEntryTaskCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function __invoke(ReindexEntryTaskCommand $command)
    {
        $this->output->write(t('Reindexing entry ID: %s', $command->getEntryId()));
        $entry = $this->objectManager->getEntry($command->getEntryId());
        if ($entry) {
            $entity = $entry->getEntity();
            $category = $entity->getAttributeKeyCategory();
            $indexer = $category->getSearchIndexer();
            $values = $category->getAttributeValues($entry);
            foreach ($values as $value) {
                $indexer->indexEntry($category, $value, $entry);
            }
        } else {
            $this->output->write(t("Unable to locate entry object for ID '%s'", $command->getEntryId()));
        }
    }

}
