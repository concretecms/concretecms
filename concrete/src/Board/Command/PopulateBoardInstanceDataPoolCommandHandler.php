<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Item\Populator\PopulatorInterface;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Doctrine\ORM\EntityManager;

class PopulateBoardInstanceDataPoolCommandHandler implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(PopulateBoardInstanceDataPoolCommand $command)
    {
        $instance = $command->getInstance();
        $board = $instance->getBoard();
        $configuredDataSources = $board->getDataSources();

        $batch = new InstanceItemBatch();
        $batch->setInstance($instance);
        $this->entityManager->persist($batch);

        foreach ($configuredDataSources as $configuredDataSource) {
            $dataSource = $configuredDataSource->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $populator = $dataSourceDriver->getItemPopulator();

            $since = $command->getRetrieveDataObjectsAfter();
            if ($since === -1) {
                // That means this is the first time we're populating the board. So we run the new
                // population routine.
                $mode = PopulatorInterface::RETRIEVE_FIRST_RUN;
            } else {
                $mode = PopulatorInterface::RETRIEVE_NEW_ITEMS;
            }

            $objects = $populator->createBoardInstanceItems($instance, $batch, $configuredDataSource, $mode);

            $this->logger->debug(
                t(/*i18n: %1$s is a number, %2$s is the name of a data source*/'Retrieved %1$s objects from %2$s data source after timestamp %3$s',
            count($objects), $dataSource->getName(), $command->getRetrieveDataObjectsAfter()
                )
            );

            $db = $this->entityManager->getConnection();
            foreach ($objects as $object) {
                $existing = $db->executeQuery('select count(boardInstanceItemID) from BoardInstanceItems
                where uniqueItemId = ? and configuredDataSourceID = ?', [
                    $object->getUniqueItemId(), $object->getDataSource()->getConfiguredDataSourceID()
                ]);
                if ($existing->fetchColumn() !== 0) {
                    $this->entityManager->persist($object);
                }
            }
        }
        $instance->setDateDataPoolLastUpdated(time());
        $this->entityManager->flush();
    }


}
