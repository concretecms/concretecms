<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

abstract class BoardSlotCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    abstract protected function getRuleType($command);

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(BoardSlotCommand $command)
    {
        $slot = $command->getSlot();
        $rule = new InstanceSlotRule();
        $rule->setInstance($command->getInstance());
        $rule->setSlot($slot);
        $rule->setDateCreated(time());
        $user = new User();
        if ($user) {
            $userInfo = $user->getUserInfoObject();
            if ($userInfo) {
                $rule->setUser($userInfo->getEntityObject());
            }
        }
        $rule->setStartDate($command->getStartDate());
        $rule->setEndDate($command->getEndDate());
        $rule->setBlockID($command->getBlockID());
        $rule->setRuleType($this->getRuleType($command));
        $this->entityManager->persist($rule);
        $this->entityManager->flush();
    }


}
