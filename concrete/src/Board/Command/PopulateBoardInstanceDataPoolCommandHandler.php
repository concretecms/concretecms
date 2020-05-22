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

    const MAX_POPULATION_DAY_INTERVAL = 60; // we go 60 days into the past.

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
                t('Retrieved %s objects from %s data source after timestamp %s',
                    count($objects), $dataSource->getName(), $command->getRetrieveDataObjectsAfter()
                ));

            foreach ($objects as $object) {
                $this->entityManager->persist($object);
            }
        }
        $instance->setDateDataPoolLastUpdated(time());
        $this->entityManager->flush();
    }


}
