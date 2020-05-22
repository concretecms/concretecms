<?php

namespace Concrete\Core\Board\Command;

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
        $batch->setBoard($board);
        $this->entityManager->persist($batch);
        foreach($configuredDataSources as $configuredDataSource) {
            $dataSource = $configuredDataSource->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $populator = $dataSourceDriver->getItemPopulator();
            $objects = $populator->createBoardInstanceItems(
                $instance, $batch, $configuredDataSource, $command->getRetrieveDataObjectsAfter()
            );
            $this->logger->debug(
                t(/*i18n: %1$s is a number, %2$s is the name of a data source*/'Retrieved %1$s objects from %2$s data source after timestamp %s',
            count($objects), $dataSource->getName(), $command->getRetrieveDataObjectsAfter()
                ));

            foreach($objects as $object) {
                $this->entityManager->persist($object);
            }
        }
        $instance->setDateDataPoolLastUpdated(time());
        $this->entityManager->flush();
    }


}
