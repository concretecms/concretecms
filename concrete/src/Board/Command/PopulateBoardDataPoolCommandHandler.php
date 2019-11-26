<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\ItemBatch;
use Doctrine\ORM\EntityManager;

class PopulateBoardDataPoolCommandHandler
{
    
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(PopulateBoardDataPoolCommand $command)
    {
        $board = $command->getBoard();
        $configuredDataSources = $board->getDataSources();
        $batch = new ItemBatch();
        $batch->setBoard($board);
        $this->entityManager->persist($batch);
        foreach($configuredDataSources as $configuredDataSource) {
            $configuration = $configuredDataSource->getConfiguration();
            $dataSource = $configuredDataSource->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $populator = $dataSourceDriver->getItemPopulator();
            $objects = $populator->createBoardItems($board, $batch, $configuredDataSource);
            foreach($objects as $object) {
                $this->entityManager->persist($object);
            }
        }
        $this->entityManager->flush();
    }

    
}
