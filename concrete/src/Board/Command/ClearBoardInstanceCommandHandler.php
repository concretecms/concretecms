<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Doctrine\ORM\EntityManager;

class ClearBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ClearBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();

        // Reset all dateAddedToBoard columns in the data pool, since they're no longer on the board.
        $qb = $this->entityManager->createQueryBuilder();
        $qb->update(InstanceItem::class, 'i')
            ->set('i.dateAddedToBoard', 0)
            ->where('i.instance = :instance');
        $qb->setParameter('instance', $instance);
        $qb->getQuery()->execute();

        $slots = $instance->getSlots();
        foreach($slots as $slot) {
            $this->entityManager->remove($slot);
        }
        $this->entityManager->flush();
    }


}
