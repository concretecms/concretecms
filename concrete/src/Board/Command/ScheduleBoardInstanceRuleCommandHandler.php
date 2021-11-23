<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;

class ScheduleBoardInstanceRuleCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(EntityManager $entityManager, LoggerFactory $loggerFactory)
    {
        $this->entityManager = $entityManager;
        $this->loggerFactory = $loggerFactory;
    }

    public function __invoke(ScheduleBoardInstanceRuleCommand $command)
    {
        $rule = $this->entityManager->find(InstanceSlotRule::class, $command->getBoardInstanceSlotRuleID());
        if ($rule) {
            $startDateTime = new \DateTime($command->getStartDate() . ' ' . $command->getStartTime() . ':00', new \DateTimeZone($command->getTimezone()));
            $endDateTime = new \DateTime($command->getEndDate() . ' ' . $command->getEndTime() . ':00', new \DateTimeZone($command->getTimezone()));
            $rule->setTimezone($command->getTimezone());
            $rule->setStartDate($startDateTime->getTimestamp());
            $rule->setEndDate($endDateTime->getTimestamp());
            $rule->setSlot($command->getSlot());
            if ($command->getName()) {
                $rule->setNotes($command->getName());
            }
            $this->entityManager->persist($rule);
            $this->entityManager->flush();
        }

        $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_BOARD);
        $logger->info(t('Instance rule {ruleID} successfully scheduled for start date {startDate} and end date {endDate} in slot {slot}.'), [
            'slot' => $command->getSlot(),
            'startDate' => $startDateTimestamp,
            'endDate' => $endDateTimestamp,
        ]);
        return $rule;
    }


}
