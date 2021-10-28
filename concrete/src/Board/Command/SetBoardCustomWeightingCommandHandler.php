<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Doctrine\ORM\EntityManager;

class SetBoardCustomWeightingCommandHandler
{
    
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(SetBoardCustomWeightingCommand $command)
    {
        foreach($command->getWeightings() as $weighting) {
            [$configuredDataSource, $weight] = $weighting;
            /**
             * @var $configuredDataSource ConfiguredDataSource
             */
            $configuredDataSource->setCustomWeight($weight);
            $this->entityManager->persist($configuredDataSource);
        }
        $board = $command->getBoard();
        $board->setHasCustomWeightingRules(true);
        $this->entityManager->persist($board);
        $this->entityManager->flush();

    }

    
}
