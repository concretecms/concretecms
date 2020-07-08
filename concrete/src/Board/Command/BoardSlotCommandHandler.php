<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;

abstract class BoardSlotCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    abstract protected function getRuleType($command);

    public function __construct(UuidGenerator $uuidGenerator, EntityManager $entityManager)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->entityManager = $entityManager;
    }

    public function handle(BoardSlotCommand $command)
    {
        $dateService = new Date();
        $timezone = $dateService->getUserTimeZoneID();
        $dateCreated = new \DateTime();
        $dateCreated->setTimezone(new \DateTimeZone($timezone));

        $slot = $command->getSlot();
        $rule = new InstanceSlotRule();
        $rule->setInstance($command->getInstance());
        $rule->setSlot($slot);
        $rule->setDateCreated($dateCreated->getTimestamp());
        $rule->setTimezone($timezone);
        $rule->setBatchIdentifier($this->uuidGenerator->generate($this->entityManager, $rule));

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
