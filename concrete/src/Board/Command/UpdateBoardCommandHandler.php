<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Doctrine\ORM\EntityManager;

class UpdateBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(UpdateBoardCommand $command)
    {
        $board = $command->getBoard();
        $board->setSite($command->getSite());
        $board->setSortBy($command->getSortBy());
        $board->setBoardName($command->getName());
        $board->setTemplate($command->getTemplate());
        $this->entityManager->persist($board);
        $this->entityManager->flush();
        
        return $board;
    }

    
}
