<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceItemBatch;
use Doctrine\ORM\EntityManager;

class PopulateBoardInstanceDataPoolCommandHandler
{

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
            $objects = $populator->createBoardInstanceItems($instance, $batch, $configuredDataSource);
            foreach($objects as $object) {
                $this->entityManager->persist($object);
            }
        }
        $this->entityManager->flush();
    }


}
