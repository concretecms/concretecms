<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Doctrine\ORM\EntityManager;

class CreateBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateBoardCommand $command)
    {
        $board = new Board();
        $board->setSite($command->getSite());
        $board->setSortBy($command->getSortBy());
        $board->setBoardName($command->getName());
        $board->setTemplate($command->getTemplate());
        $this->entityManager->persist($board);
        $this->entityManager->flush();
        
        return $board;
    }

    
}
