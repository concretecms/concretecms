<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Board\ItemBatch;
use Doctrine\ORM\EntityManager;

class ClearBoardDataPoolCommandHandler
{
    
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ClearBoardDataPoolCommand $command)
    {
        $board = $command->getBoard();
        foreach($board->getItems() as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(ItemBatch::class, 'ib')
            ->where('ib.board = :board');
        $queryBuilder->setParameter('board', $board);
        $queryBuilder->getQuery()->execute();
    }

    
}
