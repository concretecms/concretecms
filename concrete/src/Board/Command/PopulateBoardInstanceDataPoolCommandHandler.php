<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Item\Populator\PopulatorInterface;
use Concrete\Core\Entity\Board\InstanceItem;
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

            $items = $populator->createItemsFromDataSource($instance, $configuredDataSource, $mode);

            $this->logger->debug(
                t(/*i18n: %1$s is a number, %2$s is the name of a data source*/'Retrieved %1$s objects from %2$s data source after timestamp %3$s',
            count($items), $dataSource->getName(), $command->getRetrieveDataObjectsAfter()
                )
            );

            $db = $this->entityManager->getConnection();
            foreach ($items as $item) {
                $existing = $db->executeQuery('select count(boardInstanceItemID) from BoardInstanceItems bi
                inner join BoardItems i on bi.boardItemID = i.boardItemID where i.uniqueItemId = ? and bi.configuredDataSourceID = ?', [
                    $item->getUniqueItemId(), $configuredDataSource->getConfiguredDataSourceID()
                ]);
                if ($existing->fetchColumn() !== 0) {
                    $instanceItem = new InstanceItem();
                    $instanceItem->setInstance($instance);
                    $instanceItem->setDataSource($configuredDataSource);
                    $instanceItem->setBatch($batch);
                    $instanceItem->setItem($item);
                    $this->entityManager->persist($item);
                    $this->entityManager->persist($instanceItem);
                }
            }
        }
        $instance->setDateDataPoolLastUpdated(time());
        $this->entityManager->flush();
    }


}
