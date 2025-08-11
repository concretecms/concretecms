<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Logger\LoggerFactory;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Doctrine\ORM\EntityManager;

class PopulateBoardInstanceDataPoolCommandHandler
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(LoggerFactory $loggerFactory, EntityManager $entityManager)
    {
        $this->loggerFactory = $loggerFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(PopulateBoardInstanceDataPoolCommand $command)
    {
        $instance = $command->getInstance();
        $board = $instance->getBoard();
        $configuredDataSources = $board->getDataSources();

        $logger = $this->loggerFactory->createFromInstance($instance);

        $batch = new InstanceItemBatch();
        $batch->setInstance($instance);
        $this->entityManager->persist($batch);

        foreach ($configuredDataSources as $configuredDataSource) {
            $dataSource = $configuredDataSource->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $populator = $dataSourceDriver->getItemPopulator();

            $items = $populator->createItemsFromDataSource($instance, $configuredDataSource);

            $logger->write(
                t(/*i18n: %1$s is a number, %2$s is the name of a data source*/'Retrieved %1$s objects from %2$s data source',
            count($items), $dataSource->getName()
                )
            );

            $db = $this->entityManager->getConnection();
            foreach ($items as $item) {
                $existing = $db->fetchColumn('select count(boardInstanceItemID) from BoardInstanceItems bi
                inner join BoardItems i on bi.boardItemID = i.boardItemID where i.uniqueItemId = ? and bi.configuredDataSourceID = ?', [
                    $item->getUniqueItemId(), $configuredDataSource->getConfiguredDataSourceID()
                ]);
                if ($existing < 1) {
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
