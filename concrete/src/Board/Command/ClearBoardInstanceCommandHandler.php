<?php

namespace Concrete\Core\Board\Command;

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

    public function handle(ClearBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();
        $slots = $instance->getSlots();
        foreach($slots as $slot) {
            $this->entityManager->remove($slot);
        }
        $this->entityManager->flush();
    }


}
