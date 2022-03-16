<?php

namespace Concrete\Core\Board\Command;

use Doctrine\ORM\EntityManager;

class ResetBoardCustomWeightingCommandHandler
{
    
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ResetBoardCustomWeightingCommand $command)
    {
        $board = $command->getBoard();
        if ($board->hasCustomWeightingRules()) {
            foreach($board->getDataSources() as $configuredDataSource) {
                $configuredDataSource->setCustomWeight(0);
                $this->entityManager->persist($configuredDataSource);
            }
            $board->setHasCustomWeightingRules(false);
            $this->entityManager->persist($board);
            $this->entityManager->flush();
        }
    }

    
}
