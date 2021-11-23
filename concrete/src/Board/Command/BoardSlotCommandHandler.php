<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
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

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    abstract protected function getRuleType($command);

    public function __construct(UuidGenerator $uuidGenerator, EntityManager $entityManager, LoggerFactory $loggerFactory)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->entityManager = $entityManager;
        $this->loggerFactory = $loggerFactory;
    }

    public function __invoke(BoardSlotCommand $command)
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

        $instance = $command->getInstance();
        $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_BOARD);
        $logger->info(t('Instance rule for slot {slot} in instance {instanceID} successfully created with start date {startDate} and end date {endDate}. Rule Type: {ruleType} Block ID: {bID}'), [
            'slot' => $command->getSlot(),
            'instanceID' => $instance->getBoardInstanceID(),
            'startDate' => $command->getStartDate(),
            'endDate' => $command->getEndDate(),
            'bID' => $command->getBlockID(),
            'ruleType' => $this->getRuleType($command)
        ]);

        return $rule;
    }


}
