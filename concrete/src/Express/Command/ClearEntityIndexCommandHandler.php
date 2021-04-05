<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\ObjectManager;

class ClearEntityIndexCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(ObjectManager $objectManager, Connection $connection)
    {
        $this->objectManager = $objectManager;
        $this->connection = $connection;
    }


    public function __invoke(ClearEntityIndexCommand $command)
    {
        $entity = $this->objectManager->getObjectByID($command->getEntityId());
        if ($entity) {
            /**
             * @var $entity Entity
             */
            $this->output->write(t("Clearing express index for '%s' (ID: '%s')", $entity->getName(), $entity->getId()));
            $category = $entity->getAttributeKeyCategory();
            $table = $category->getIndexedSearchTable();
            if ($table) {
                if ($this->connection->tableExists($table)) {
                    $this->connection->executeUpdate(sprintf('truncate table %s', $table));
                }
            }
        }
    }

}
