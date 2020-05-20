<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class PinSlotToBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(PinSlotToBoardCommand $command)
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
        $rule->setBlockID($command->getBlockID());
        $rule->setRuleType($rule::RULE_TYPE_PINNED);
        $this->entityManager->persist($rule);
        $this->entityManager->flush();
    }


}
