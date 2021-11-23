<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Board\Designer\Command\AddDesignerSlotToBoardCommand;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class AddDesignerSlotToBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AddDesignerSlotToBoardCommand $command)
    {
        $dateCreated = new \DateTime();
        $dateCreated->setTimezone(new \DateTimeZone($command->getTimezone()));

        $slot = $command->getSlot();
        $rule = new InstanceSlotRule();
        $rule->setInstance($command->getInstance());
        $rule->setSlot($slot);
        $rule->setDateCreated($dateCreated->getTimestamp());
        $rule->setTimezone($command->getTimezone());
        $rule->setUser($command->getUser());
        $rule->setIsLocked($command->isLocked());
        $rule->setBatchIdentifier($command->getBatchIdentifier());

        $rule->setStartDate($command->getStartDate());
        $rule->setEndDate($command->getEndDate());
        $rule->setBlockID($command->getBlockID());
        $rule->setRuleType($command->getRuleType());
        $rule->setNotes($command->getNotes());
        $this->entityManager->persist($rule);
        $this->entityManager->flush();
    }


}
