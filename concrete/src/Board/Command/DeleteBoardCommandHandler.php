<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Doctrine\ORM\EntityManager;

class DeleteBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteBoardCommand $command)
    {
        $board = $command->getBoard();
        $this->entityManager->remove($board);
        $this->entityManager->flush();
        
        return $board;
    }

    
}
