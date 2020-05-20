<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Doctrine\ORM\EntityManager;

class UnpinSlotFromBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(UnpinSlotFromBoardCommand $command)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(InstanceSlotRule::class, 'r')
            ->where('r.ruleType = :ruleType')
            ->andWhere('r.slot = :slot');
        $qb->setParameter('ruleType', InstanceSlotRule::RULE_TYPE_PINNED);
        $qb->setParameter('slot', $command->getSlot());
        $qb->getQuery()->execute();
    }


}
